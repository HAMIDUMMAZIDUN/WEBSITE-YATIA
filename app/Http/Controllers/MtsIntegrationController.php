<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\AuditLog;

// Helper to detect if a string is RDM AES ciphertext (base64-encoded binary)
function rdm_is_encrypted(string $value): bool
{
    // RDM encrypted values are base64-encoded and typically contain '=' padding
    // and do NOT look like normal names (letters, spaces, dots, commas)
    if (empty($value)) return false;
    if (preg_match('/^[A-Za-zÀ-ÿ\s\.,\'\-()]+$/', $value)) return false; // plaintext name
    // Likely encrypted if it matches base64 pattern and contains non-name chars
    return (bool) preg_match('/^[A-Za-z0-9+\/]+=*$/', trim($value));
}

class MtsIntegrationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $connected = false;
        $errorMessage = null;
        $externalCounts = [
            'mapel' => 0,
            'kelas' => 0,
            'guru' => 0,
            'siswa' => 0,
        ];

        try {
            $mtsDb = DB::connection('mts');
            $mtsDb->getPdo();
            $connected = true;

            $externalCounts['mapel'] = $mtsDb->table('e_mapel')->count();
            $externalCounts['kelas'] = $mtsDb->table('e_kelas')->count();
            $externalCounts['guru'] = $mtsDb->table('e_guru')->count();
            $externalCounts['siswa'] = $mtsDb->table('e_siswa')->count();
        } catch (\Exception $e) {
            $connected = false;
            $errorMessage = $e->getMessage();
        }

        $localCounts = [
            'mapel' => MataPelajaran::count(),
            'kelas' => Kelas::count(),
            'guru' => Guru::count(),
            'siswa' => Siswa::count(),
        ];

        $latestSyncs = AuditLog::where('action', 'like', 'Sync%from MTs Database')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin.integrasi_mts', compact(
            'connected',
            'errorMessage',
            'externalCounts',
            'localCounts',
            'latestSyncs'
        ));
    }

    public function sync(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $type = $request->input('type');
        if (!in_array($type, ['mapel', 'kelas', 'guru', 'siswa', 'all'])) {
            return redirect()->back()->with('error', 'Tipe sinkronisasi tidak valid.');
        }

        // Set longer execution time for larger sync operations (e.g. students)
        set_time_limit(600);

        try {
            $synced = [];
            
            if ($type === 'mapel' || $type === 'all') {
                $synced['mapel'] = $this->syncMapel();
            }
            if ($type === 'guru' || $type === 'all') {
                $synced['guru'] = $this->syncGuru();
            }
            if ($type === 'kelas' || $type === 'all') {
                $synced['kelas'] = $this->syncKelas();
            }
            if ($type === 'siswa' || $type === 'all') {
                $synced['siswa'] = $this->syncSiswa();
            }

            // Create audit log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'Sync ' . $type . ' from MTs Database',
                'entity' => 'integration',
                'entity_id' => 1,
                'old_value' => null,
                'new_value' => json_encode($synced),
            ]);

            $message = 'Sinkronisasi berhasil diselesaikan. ';
            foreach ($synced as $t => $count) {
                $message .= ucfirst($t) . ': ' . $count . ' data synced. ';
            }

            return redirect()->route('admin.integrasi.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.integrasi.index')->with('error', 'Gagal melakukan sinkronisasi: ' . $e->getMessage());
        }
    }

    private function syncMapel()
    {
        $external = DB::connection('mts')->table('e_mapel')->get();
        $count = 0;
        foreach ($external as $ex) {
            if (empty($ex->mapel_nama)) continue;
            
            $kode = $ex->mapel_alias ?: ('MP-' . $ex->mapel_id);
            
            MataPelajaran::updateOrCreate(
                ['id' => $ex->mapel_id],
                [
                    'nama_mapel' => $ex->mapel_nama,
                    'kode_mapel' => $kode,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncGuru()
    {
        $external = DB::connection('mts')->table('e_guru')->get();
        $count = 0;
        $guruRole = Role::where('name', 'guru')->first();
        if (!$guruRole) {
            throw new \Exception('Role "guru" tidak ditemukan di database.');
        }

        foreach ($external as $ex) {
            // Resolve guru name — may be encrypted in older RDM versions
            $guruNama = (isset($ex->guru_nama) && !rdm_is_encrypted((string) $ex->guru_nama))
                ? $ex->guru_nama
                : 'Guru #' . $ex->guru_id;

            if (empty($guruNama)) continue;

            $email = 'guru.' . $ex->guru_id . '@gmail.com';

            // Derive NIK from NIP (primary login identifier for guru)
            $nip = $ex->guru_nip ?? '';
            $guruNik = (!empty($nip) && !str_starts_with($nip, 'NIP-'))
                ? $nip
                : ('G' . $ex->guru_id);

            $user = User::where('email', $email)->first();
            if (!$user) {
                // Use bcrypt hash from RDM if available, otherwise default
                $password = $ex->password && str_starts_with((string) $ex->password, '$2y$')
                    ? $ex->password
                    : Hash::make('password123');

                $user = User::create([
                    'name' => $guruNama,
                    'nik' => $guruNik,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $guruRole->id,
                ]);
            } else {
                $user->update(['name' => $guruNama, 'nik' => $guruNik]);
            }

            $gender = (strtoupper((string) ($ex->guru_gender ?? '')) === 'P' || ($ex->guru_gender ?? '') === 'Perempuan') ? 'P' : 'L';

            Guru::updateOrCreate(
                ['id' => $ex->guru_id],
                [
                    'user_id' => $user->id,
                    'nip' => $ex->guru_nip ?: 'NIP-' . $ex->guru_id,
                    'nik_nuptk' => $ex->guru_nuptk ?? null,
                    'nama' => $guruNama,
                    'jenis_kelamin' => $gender,
                    'tempat_lahir' => $ex->guru_tempat ?? null,
                    'tanggal_lahir' => $ex->guru_tgllahir ?? null,
                    'pendidikan' => $ex->guru_pendidikan ?? null,
                    'password_plain' => 'password123',
                    'is_aktif' => $ex->guru_aktif ?? 1,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncKelas()
    {
        $external = DB::connection('mts')->table('e_kelas')->get();
        $count = 0;
        foreach ($external as $ex) {
            if (empty($ex->kelas_nama)) continue;

            $waliKelasId = null;
            if ($ex->guru_id && Guru::where('id', $ex->guru_id)->exists()) {
                $waliKelasId = $ex->guru_id;
            }

            Kelas::updateOrCreate(
                ['id' => $ex->kelas_id],
                [
                    'nama_kelas' => $ex->kelas_nama,
                    'wali_kelas_id' => $waliKelasId,
                ]
            );
            $count++;
        }

        // Auto update Double-Role for Wali Kelas
        $waliKelasIds = Kelas::whereNotNull('wali_kelas_id')->pluck('wali_kelas_id')->toArray();
        $waliRole = Role::where('name', 'wali_kelas_guru')->first() ?? Role::where('name', 'wali_kelas')->first();
        if ($waliRole) {
            $gurus = Guru::whereIn('id', $waliKelasIds)->get();
            foreach ($gurus as $g) {
                $g->user->update(['role_id' => $waliRole->id]);
            }
        }

        return $count;
    }

    private function syncSiswa()
    {
        $external = DB::connection('mts')->table('e_siswa')->get();
        $count = 0;
        $siswaRole = Role::where('name', 'absensi')->first();
        if (!$siswaRole) {
            throw new \Exception('Role "absensi" tidak ditemukan di database.');
        }

        foreach ($external as $ex) {
            $gender = (strtoupper((string) ($ex->siswa_gender ?? '')) === 'P'
                || ($ex->siswa_gender ?? '') === 'Perempuan'
                || ($ex->siswa_gender ?? '') === 'perempuan') ? 'P' : 'L';

            // RDM encrypts siswa_nama. Build a readable placeholder from plaintext fields.
            $namaEncrypted = rdm_is_encrypted((string) ($ex->siswa_nama ?? ''));
            if ($namaEncrypted) {
                // Compose placeholder: use NIS + parent names (plaintext in RDM)
                $nis = ($ex->siswa_nis && $ex->siswa_nis != '0') ? $ex->siswa_nis : $ex->siswa_id;
                $genderLabel = $gender === 'P' ? 'Siswi' : 'Siswa';
                $ayah = $ex->nama_ayah ?? '';
                $ibu  = $ex->nama_ibu  ?? '';
                $parentInfo = trim(implode(' / ', array_filter([$ayah, $ibu])));
                $displayName = $genderLabel . ' NIS:' . $nis . ($parentInfo ? ' (' . $parentInfo . ')' : '');
            } else {
                $displayName = $ex->siswa_nama;
            }

            if (empty($displayName)) continue;

            // NISN may also be encrypted
            $nisnRaw = (string) ($ex->siswa_nisn ?? '');
            $nisn = rdm_is_encrypted($nisnRaw) ? ('NISN-' . $ex->siswa_id) : ($nisnRaw ?: ('NISN-' . $ex->siswa_id));

            $email = 'siswa.' . $ex->siswa_id . '@gmail.com';

            // NIK for siswa = NIS (login identifier)
            $siswaRawNis = ($ex->siswa_nis && $ex->siswa_nis != '0') ? (string) $ex->siswa_nis : null;
            $siswaLoginNik = $siswaRawNis ?? ('S' . $ex->siswa_id);

            $user = User::where('email', $email)->first();
            if (!$user) {
                // Use bcrypt hash only if it really is bcrypt
                $pwdRaw = (string) ($ex->password ?? '');
                $password = str_starts_with($pwdRaw, '$2y$')
                    ? $pwdRaw
                    : Hash::make('password123');

                $user = User::create([
                    'name' => $displayName,
                    'nik' => $siswaLoginNik,
                    'email' => $email,
                    'password' => $password,
                    'role_id' => $siswaRole->id,
                ]);
            } else {
                $user->update(['name' => $displayName, 'nik' => $siswaLoginNik]);
            }

            $kelasId = null;
            if (!empty($ex->kelas_id) && Kelas::where('id', $ex->kelas_id)->exists()) {
                $kelasId = $ex->kelas_id;
            }

            Siswa::updateOrCreate(
                ['id' => $ex->siswa_id],
                [
                    'user_id' => $user->id,
                    'nis' => ($ex->siswa_nis && $ex->siswa_nis != '0') ? $ex->siswa_nis : ('NIS-' . $ex->siswa_id),
                    'nisn' => $nisn,
                    'nama' => $displayName,
                    'jenis_kelamin' => $gender,
                    'kelas_id' => $kelasId,
                    'alamat' => $ex->siswa_alamat ?? null,
                    'no_hp' => $ex->siswa_telpon ?? null,
                    'status' => 'Aktif',
                ]
            );
            $count++;
        }
        return $count;
    }
}
