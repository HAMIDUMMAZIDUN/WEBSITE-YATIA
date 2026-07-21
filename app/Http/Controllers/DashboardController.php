<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;
use App\Models\MateriTugas;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\PelanggaranBk;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isWaliKelas()) {
            return $this->waliKelasDashboard();
        } elseif ($user->isGuru()) {
            return $this->guruDashboard();
        } elseif ($user->isSiswa()) {
            return $this->siswaDashboard();
        } elseif ($user->isGuruBk()) {
            return $this->guruBkDashboard();
        }

        abort(403, 'Peran tidak dikenali.');
    }

    private function superAdminDashboard()
    {
        $data = [
            'counts' => [
                'users' => User::count(),
                'gurus' => Guru::count(),
                'siswas' => Siswa::count(),
                'kelas' => Kelas::count(),
                'mapel' => MataPelajaran::count(),
            ],
            'users' => User::with('role')->get(),
            'gurus' => Guru::with('user')->get(),
            'siswas' => Siswa::with(['user', 'kelas'])->get(),
            'kelas' => Kelas::with('waliGuru')->get(),
            'mapel' => MataPelajaran::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.super_admin', $data);
    }

    private function guruDashboard()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $data = [
            'guru' => $guru,
            'jadwals' => JadwalPelajaran::with(['kelas', 'mataPelajaran'])->where('guru_id', $guru->id)->get(),
            'materiTugas' => MateriTugas::with(['kelas', 'mataPelajaran'])->where('guru_id', $guru->id)->get(),
            'kelas' => Kelas::all(),
            'mapels' => MataPelajaran::all(),
            'siswas' => Siswa::all(),
        ];
        return view('dashboard.guru', $data);
    }

    private function waliKelasDashboard()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $kelas = Kelas::where('wali_kelas_id', $guru->id)->first();
        if (!$kelas) {
            $kelas = Kelas::first();
        }

        $siswas = $kelas ? Siswa::where('kelas_id', $kelas->id)->get() : collect();
        $siswaIds = $siswas->pluck('id');

        $data = [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'presensi' => Presensi::with('siswa')->whereIn('siswa_id', $siswaIds)->get(),
            'nilais' => Nilai::with(['siswa', 'mataPelajaran'])->whereIn('siswa_id', $siswaIds)->get(),
        ];
        return view('dashboard.wali_kelas', $data);
    }

    private function siswaDashboard()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(404, 'Detail siswa tidak ditemukan.');
        }

        $data = [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'jadwals' => JadwalPelajaran::with(['mataPelajaran', 'guru'])->where('kelas_id', $siswa->kelas_id)->get(),
            'materiTugas' => MateriTugas::with(['mataPelajaran', 'guru'])->where('kelas_id', $siswa->kelas_id)->get(),
            'nilais' => Nilai::with(['mataPelajaran', 'guru'])->where('siswa_id', $siswa->id)->get(),
            'presensis' => Presensi::where('siswa_id', $siswa->id)->get(),
            'pelanggarans' => PelanggaranBk::with('guruBk')->where('siswa_id', $siswa->id)->get(),
        ];
        return view('dashboard.siswa', $data);
    }

    private function guruBkDashboard()
    {
        $user = Auth::user();
        $guru = $user->guru;

        $data = [
            'guru' => $guru,
            'siswas' => Siswa::with('kelas')->get(),
            'pelanggarans' => PelanggaranBk::with(['siswa', 'guruBk'])->get(),
            'gurus' => Guru::all(),
        ];
        return view('dashboard.guru_bk', $data);
    }

    // ACTIONS FOR DATA MANAGEMENT

    // 1. Super Admin Actions
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@gmail.com')) {
                        $fail('Email harus menggunakan domain @gmail.com.');
                        return;
                    }
                    $domain = substr(strrchr($value, "@"), 1);
                    if ($domain && !checkdnsrr($domain, "MX")) {
                        $fail('Domain email tidak valid atau tidak memiliki server mail (MX) aktif.');
                    }
                }
            ],
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        // If user is a guru, wali_kelas or guru_bk, automatically create placeholder Guru record
        $role = Role::find($request->role_id);
        if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
            Guru::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nip' => 'NIP-' . time(),
            ]);
        } elseif ($role->name === 'siswa') {
            Siswa::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nis' => 'NIS-' . time(),
            ]);
        }

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus diri sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create($request->all());
        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function storeMapel(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajarans',
        ]);

        MataPelajaran::create($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    // 2. Guru Actions
    public function storeMateriTugas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:materi,tugas',
            'deskripsi' => 'nullable|string',
            'batas_waktu' => 'nullable|date',
        ]);

        $guru = Auth::user()->guru;
        MateriTugas::create([
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $guru->id,
            'judul' => $request->judul,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'batas_waktu' => $request->batas_waktu,
        ]);

        return redirect()->back()->with('success', 'Materi/Tugas berhasil diunggah.');
    }

    public function storeNilai(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tipe_nilai' => 'required|string',
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $guru = Auth::user()->guru;
        Nilai::create([
            'siswa_id' => $request->siswa_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => $guru->id,
            'tipe_nilai' => $request->tipe_nilai,
            'nilai' => $request->nilai,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Nilai siswa berhasil diinput.');
    }

    // Guru: Input Jadwal Pelajaran (appears in Siswa jadwal page automatically)
    public function storeJadwalGuru(Request $request)
    {
        $request->validate([
            'kelas_id'         => 'required|exists:kelas,id',
            'mata_pelajaran_id'=> 'required|exists:mata_pelajarans,id',
            'hari'             => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'        => 'required|date_format:H:i',
            'jam_selesai'      => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $guru = Auth::user()->guru;
        JadwalPelajaran::create([
            'guru_id'          => $guru->id,
            'kelas_id'         => $request->kelas_id,
            'mata_pelajaran_id'=> $request->mata_pelajaran_id,
            'hari'             => $request->hari,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan dan otomatis tampil di halaman siswa.');
    }

    // Wali Kelas: Input Presensi Siswa (appears in Siswa presensi page automatically)
    public function storePresensi(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal'  => 'required|date',
            'status'   => 'required|in:hadir,sakit,izin,alpa',
        ]);

        // Upsert: update if same siswa+date already exists
        Presensi::updateOrCreate(
            ['siswa_id' => $request->siswa_id, 'tanggal' => $request->tanggal],
            ['status'   => $request->status]
        );

        return redirect()->back()->with('success', 'Presensi siswa berhasil dicatat dan otomatis sinkron ke halaman siswa.');
    }

    // 3. BK Actions
    public function storePelanggaran(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'pelanggaran' => 'required|string',
            'poin' => 'required|integer|min:1',
            'tindakan' => 'nullable|string',
            'status' => 'required|in:diproses,diselesaikan,panggilan_orang_tua',
        ]);

        $guruBK = Auth::user()->guru;
        $pelanggaran = PelanggaranBk::create([
            'siswa_id' => $request->siswa_id,
            'guru_bk_id' => $guruBK->id,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'pelanggaran' => $request->pelanggaran,
            'poin' => $request->poin,
            'tindakan' => $request->tindakan,
            'status' => $request->status,
        ]);

        // Increment student's BK points
        $siswa = Siswa::find($request->siswa_id);
        $siswa->increment('poin_bk', $request->poin);

        return redirect()->back()->with('success', 'Pelanggaran BK berhasil dicatat.');
    }

    public function updatePelanggaranStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,diselesaikan,panggilan_orang_tua',
            'tindakan' => 'nullable|string',
        ]);

        $pelanggaran = PelanggaranBk::findOrFail($id);
        $pelanggaran->update([
            'status' => $request->status,
            'tindakan' => $request->tindakan,
        ]);

        return redirect()->back()->with('success', 'Status Pelanggaran BK diperbarui.');
    }


    // ─── GURU: Jadwal CRUD ────────────────────────────────────────────────────
    public function updateJadwalGuru(Request $request, $id)
    {
        $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'hari'              => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'         => 'required|date_format:H:i',
            'jam_selesai'       => 'required|date_format:H:i|after:jam_mulai',
        ]);
        JadwalPelajaran::findOrFail($id)->update($request->only([
            'kelas_id','mata_pelajaran_id','hari','jam_mulai','jam_selesai'
        ]));
        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function deleteJadwalGuru($id)
    {
        JadwalPelajaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }

    // ─── GURU: Materi & Tugas CRUD ────────────────────────────────────────────
    public function updateMateriTugas(Request $request, $id)
    {
        $request->validate([
            'judul'             => 'required|string|max:255',
            'tipe'              => 'required|in:materi,tugas',
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'deskripsi'         => 'nullable|string',
            'batas_waktu'       => 'nullable|date',
        ]);
        MateriTugas::findOrFail($id)->update($request->only([
            'judul','tipe','kelas_id','mata_pelajaran_id','deskripsi','batas_waktu'
        ]));
        return redirect()->back()->with('success', 'Materi/Tugas berhasil diperbarui.');
    }

    public function deleteMateriTugas($id)
    {
        MateriTugas::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Materi/Tugas berhasil dihapus.');
    }

    // ─── GURU: Nilai CRUD ─────────────────────────────────────────────────────
    public function updateNilai(Request $request, $id)
    {
        $request->validate([
            'tipe_nilai'        => 'required|string',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'nilai'             => 'required|numeric|min:0|max:100',
            'catatan'           => 'nullable|string',
        ]);
        Nilai::findOrFail($id)->update($request->only([
            'tipe_nilai','mata_pelajaran_id','nilai','catatan'
        ]));
        return redirect()->back()->with('success', 'Nilai berhasil diperbarui.');
    }

    public function deleteNilai($id)
    {
        Nilai::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nilai berhasil dihapus.');
    }

    // ─── WALI KELAS: Presensi CRUD ────────────────────────────────────────────
    public function updatePresensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin,alpa',
        ]);
        Presensi::findOrFail($id)->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Presensi berhasil diperbarui.');
    }

    public function deletePresensi($id)
    {
        Presensi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
    }

    // ─── GURU BK: Pelanggaran CRUD ────────────────────────────────────────────
    public function updatePelanggaran(Request $request, $id)
    {
        $request->validate([
            'pelanggaran' => 'required|string',
            'poin'        => 'required|integer|min:1',
            'tindakan'    => 'nullable|string',
            'status'      => 'required|in:diproses,diselesaikan,panggilan_orang_tua',
        ]);
        $record = PelanggaranBk::findOrFail($id);
        $oldPoin = $record->poin;
        $newPoin = $request->poin;
        $record->update($request->only(['pelanggaran','poin','tindakan','status']));
        // Adjust student's accumulated BK points
        if ($newPoin !== $oldPoin) {
            $siswa = Siswa::find($record->siswa_id);
            $siswa->poin_bk = max(0, $siswa->poin_bk - $oldPoin + $newPoin);
            $siswa->save();
        }
        return redirect()->back()->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function deletePelanggaran($id)
    {
        $record = PelanggaranBk::findOrFail($id);
        // Decrement student's poin_bk when a violation is deleted
        $siswa = Siswa::find($record->siswa_id);
        if ($siswa) {
            $siswa->poin_bk = max(0, $siswa->poin_bk - $record->poin);
            $siswa->save();
        }
        $record->delete();
        return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus dan poin siswa disesuaikan.');
    }

    // Export Data to CSV

    public function exportData($type)
    {
        $filename = $type . '_export_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            if ($type === 'users') {
                fputcsv($file, ['Nama', 'Email', 'Hak Akses']);
                $users = User::with('role')->get();
                foreach ($users as $u) {
                    fputcsv($file, [$u->name, $u->email, $u->role->name]);
                }
            } elseif ($type === 'kelas') {
                fputcsv($file, ['Nama Kelas', 'Nama Wali Kelas', 'NIP Wali Kelas']);
                $kelas = Kelas::with('waliGuru')->get();
                foreach ($kelas as $k) {
                    fputcsv($file, [
                        $k->nama_kelas, 
                        $k->waliGuru ? $k->waliGuru->nama : '-',
                        $k->waliGuru ? $k->waliGuru->nip : '-'
                    ]);
                }
            } elseif ($type === 'mapel') {
                fputcsv($file, ['Nama Mapel', 'Kode Mapel']);
                $mapels = MataPelajaran::all();
                foreach ($mapels as $m) {
                    fputcsv($file, [$m->nama_mapel, $m->kode_mapel]);
                }
            } elseif ($type === 'materi_tugas') {
                fputcsv($file, ['Nama Kelas', 'Nama Mapel', 'Kode Mapel', 'Judul', 'Tipe', 'Deskripsi', 'Batas Waktu']);
                $guru = Auth::user()->guru;
                if ($guru) {
                    $materi = MateriTugas::with(['kelas', 'mataPelajaran'])->where('guru_id', $guru->id)->get();
                    foreach ($materi as $m) {
                        fputcsv($file, [
                            $m->kelas->nama_kelas ?? '-',
                            $m->mataPelajaran->nama_mapel ?? '-',
                            $m->mataPelajaran->kode_mapel ?? '-',
                            $m->judul,
                            $m->tipe,
                            $m->deskripsi,
                            $m->batas_waktu
                        ]);
                    }
                }
            } elseif ($type === 'pelanggaran') {
                fputcsv($file, ['NIS Siswa', 'Nama Siswa', 'Tanggal', 'Pelanggaran', 'Poin', 'Tindakan', 'Status']);
                $pelanggarans = PelanggaranBk::with(['siswa'])->get();
                foreach ($pelanggarans as $p) {
                    fputcsv($file, [
                        $p->siswa->nis ?? '-',
                        $p->siswa->nama ?? '-',
                        $p->tanggal,
                        $p->pelanggaran,
                        $p->poin,
                        $p->tindakan,
                        $p->status
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Download Import CSV Template
    public function downloadTemplate($type)
    {
        $filename = $type . '_template.csv';
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            if ($type === 'users') {
                fputcsv($file, ['Nama', 'Email', 'Password', 'Role (super_admin/guru/wali_kelas/siswa/guru_bk/wali_kelas_guru)']);
                fputcsv($file, ['John Doe', 'johndoe@gmail.com', 'password123', 'siswa']);
                fputcsv($file, ['Jane Smith, M.Pd', 'janesmith@gmail.com', 'password123', 'guru']);
            } elseif ($type === 'kelas') {
                fputcsv($file, ['Nama Kelas', 'NIP Wali Kelas']);
                fputcsv($file, ['XII IPA 3', '198205122008042003']);
            } elseif ($type === 'mapel') {
                fputcsv($file, ['Nama Mapel', 'Kode Mapel']);
                fputcsv($file, ['Fisika', 'FIS-01']);
                fputcsv($file, ['Kimia', 'KIM-02']);
            } elseif ($type === 'materi_tugas') {
                fputcsv($file, ['Nama Kelas', 'Kode Mapel', 'Judul', 'Tipe (materi/tugas)', 'Deskripsi', 'Batas Waktu (YYYY-MM-DD)']);
                fputcsv($file, ['X IPA 1', 'MTK-01', 'Tugas Aljabar 1', 'tugas', 'Kerjakan soal halaman 15', '2026-08-30']);
            } elseif ($type === 'pelanggaran') {
                fputcsv($file, ['NIS Siswa', 'Pelanggaran', 'Poin', 'Tindakan', 'Status (diproses/diselesaikan/panggilan_orang_tua)']);
                fputcsv($file, ['10023', 'Terlambat masuk sekolah', '5', 'Teguran lisan', 'diproses']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Import Data from CSV
    public function importData(Request $request, $type)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        $importedCount = 0;
        $errors = [];
        $rowCount = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowCount++;
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                if ($type === 'users') {
                    if (count($row) < 4) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $password = trim($row[2]);
                    $roleName = trim($row[3]);

                    if (User::where('email', $email)->exists()) {
                        throw new \Exception("Email '$email' sudah terdaftar (Baris $rowCount)");
                    }

                    $role = Role::where('name', $roleName)->first();
                    if (!$role) {
                        throw new \Exception("Role '$roleName' tidak valid (Baris $rowCount)");
                    }

                    if (!str_ends_with(strtolower($email), '@gmail.com')) {
                        throw new \Exception("Email harus @gmail.com (Baris $rowCount)");
                    }

                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role_id' => $role->id,
                    ]);

                    if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
                        Guru::create([
                            'user_id' => $user->id,
                            'nama' => $user->name,
                            'nip' => 'NIP-' . time() . '-' . rand(10, 99),
                        ]);
                    } elseif ($role->name === 'siswa') {
                        $defaultKelas = Kelas::first();
                        Siswa::create([
                            'user_id' => $user->id,
                            'nama' => $user->name,
                            'nis' => 'NIS-' . time() . '-' . rand(10, 99),
                            'kelas_id' => $defaultKelas ? $defaultKelas->id : null,
                        ]);
                    }
                    $importedCount++;

                } elseif ($type === 'kelas') {
                    if (count($row) < 2) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $namaKelas = trim($row[0]);
                    $nipWali = trim($row[1]);

                    $waliId = null;
                    if (!empty($nipWali)) {
                        $guru = Guru::where('nip', $nipWali)->first();
                        if ($guru) {
                            $waliId = $guru->id;
                        }
                    }

                    Kelas::create([
                        'nama_kelas' => $namaKelas,
                        'wali_kelas_id' => $waliId,
                    ]);
                    $importedCount++;

                } elseif ($type === 'mapel') {
                    if (count($row) < 2) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $namaMapel = trim($row[0]);
                    $kodeMapel = trim($row[1]);

                    if (MataPelajaran::where('kode_mapel', $kodeMapel)->exists()) {
                        throw new \Exception("Kode Mapel '$kodeMapel' sudah ada (Baris $rowCount)");
                    }

                    MataPelajaran::create([
                        'nama_mapel' => $namaMapel,
                        'kode_mapel' => $kodeMapel,
                    ]);
                    $importedCount++;

                } elseif ($type === 'materi_tugas') {
                    if (count($row) < 4) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $namaKelas = trim($row[0]);
                    $kodeMapel = trim($row[1]);
                    $judul = trim($row[2]);
                    $tipe = strtolower(trim($row[3]));
                    $deskripsi = isset($row[4]) ? trim($row[4]) : null;
                    $batasWaktu = isset($row[5]) ? trim($row[5]) : null;

                    $kelas = Kelas::where('nama_kelas', $namaKelas)->first();
                    $mapel = MataPelajaran::where('kode_mapel', $kodeMapel)->first();
                    $guru = Auth::user()->guru;

                    if (!$kelas) {
                        throw new \Exception("Kelas '$namaKelas' tidak ditemukan (Baris $rowCount)");
                    }
                    if (!$mapel) {
                        throw new \Exception("Mapel dengan Kode '$kodeMapel' tidak ditemukan (Baris $rowCount)");
                    }
                    if (!in_array($tipe, ['materi', 'tugas'])) {
                        throw new \Exception("Tipe must be 'materi' or 'tugas' (Baris $rowCount)");
                    }

                    MateriTugas::create([
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'guru_id' => $guru ? $guru->id : null,
                        'judul' => $judul,
                        'tipe' => $tipe,
                        'deskripsi' => $deskripsi,
                        'batas_waktu' => $batasWaktu,
                    ]);
                    $importedCount++;

                } elseif ($type === 'pelanggaran') {
                    if (count($row) < 3) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $nisSiswa = trim($row[0]);
                    $txtPelanggaran = trim($row[1]);
                    $poinVal = intval(trim($row[2]));
                    $tindakan = isset($row[3]) ? trim($row[3]) : null;
                    $status = isset($row[4]) ? trim($row[4]) : 'diproses';

                    $siswa = Siswa::where('nis', $nisSiswa)->first();
                    if (!$siswa) {
                        throw new \Exception("Siswa dengan NIS '$nisSiswa' tidak ditemukan (Baris $rowCount)");
                    }

                    $guruBK = Auth::user()->guru;
                    PelanggaranBk::create([
                        'siswa_id' => $siswa->id,
                        'guru_bk_id' => $guruBK ? $guruBK->id : null,
                        'tanggal' => Carbon::now()->format('Y-m-d'),
                        'pelanggaran' => $txtPelanggaran,
                        'poin' => $poinVal,
                        'tindakan' => $tindakan,
                        'status' => $status,
                    ]);

                    $siswa->increment('poin_bk', $poinVal);
                    $importedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        fclose($handle);

        if (count($errors) > 0) {
            $errMsg = "Berhasil mengimpor $importedCount data. Gagal mengimpor " . count($errors) . " baris. Detail kesalahan: " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $errMsg .= " dan " . (count($errors) - 5) . " kesalahan lainnya.";
            }
            return redirect()->back()->with('error', $errMsg);
        }

        return redirect()->back()->with('success', "Berhasil mengimpor $importedCount data.");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@gmail.com')) {
                        $fail('Email harus menggunakan domain @gmail.com.');
                        return;
                    }
                    $domain = substr(strrchr($value, "@"), 1);
                    if ($domain && !checkdnsrr($domain, "MX")) {
                        $fail('Domain email tidak valid atau tidak memiliki server mail (MX) aktif.');
                    }
                }
            ],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update or create corresponding Guru or Siswa profile depending on new role
        $role = Role::find($request->role_id);
        if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
            if (!$user->guru) {
                Guru::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'nip' => 'NIP-' . time(),
                ]);
            } else {
                $user->guru->update(['nama' => $user->name]);
            }
        } elseif ($role->name === 'siswa') {
            if (!$user->siswa) {
                $defaultKelas = Kelas::first();
                Siswa::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'nis' => 'NIS-' . time(),
                    'kelas_id' => $defaultKelas ? $defaultKelas->id : null,
                ]);
            } else {
                $user->siswa->update(['nama' => $user->name]);
            }
        }

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function kelasIndex()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $data = [
            'kelas' => Kelas::with('waliGuru')->get(),
            'gurus' => Guru::all(),
        ];
        return view('dashboard.kelas', $data);
    }

    public function updateKelas(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        $kelas->update($request->all());
        return redirect()->back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function mapelIndex()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $data = [
            'mapel' => MataPelajaran::all(),
        ];
        return view('dashboard.mapel', $data);
    }

    public function updateMapel(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajarans,kode_mapel,' . $mapel->id,
        ]);

        $mapel->update($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function deleteMapel($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function waliSiswa()
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $kelas = Kelas::where('wali_kelas_id', $guru->id)->first();
        if (!$kelas) {
            $kelas = Kelas::first();
        }

        $siswas = $kelas ? Siswa::where('kelas_id', $kelas->id)->get() : collect();
        $siswaIds = $siswas->pluck('id');

        $data = [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'nilais' => Nilai::whereIn('siswa_id', $siswaIds)->get(),
        ];

        return view('dashboard.wali_siswa', $data);
    }

    public function waliAbsensi()
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $kelas = Kelas::where('wali_kelas_id', $guru->id)->first();
        if (!$kelas) {
            $kelas = Kelas::first();
        }

        $siswas = $kelas ? Siswa::where('kelas_id', $kelas->id)->get() : collect();
        $siswaIds = $siswas->pluck('id');

        $data = [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'presensi' => Presensi::whereIn('siswa_id', $siswaIds)->get(),
        ];

        return view('dashboard.wali_absensi', $data);
    }

    public function siswaElearning()
    {
        $user = Auth::user();
        if (!$user->isSiswa()) {
            abort(403);
        }
        $siswa = $user->siswa;
        if (!$siswa) {
            abort(404, 'Detail siswa tidak ditemukan.');
        }

        $data = [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'materiTugas' => MateriTugas::with(['mataPelajaran', 'guru'])->where('kelas_id', $siswa->kelas_id)->get(),
        ];

        return view('dashboard.siswa_elearning', $data);
    }

    public function siswaCbt()
    {
        $user = Auth::user();
        if (!$user->isSiswa()) {
            abort(403);
        }
        $siswa = $user->siswa;
        if (!$siswa) {
            abort(404, 'Detail siswa tidak ditemukan.');
        }

        $data = [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
        ];

        return view('dashboard.siswa_cbt', $data);
    }

    public function siswaJadwal()
    {
        $user = Auth::user();
        if (!$user->isSiswa()) {
            abort(403);
        }
        $siswa = $user->siswa;
        if (!$siswa) {
            abort(404, 'Detail siswa tidak ditemukan.');
        }

        $data = [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'jadwals' => JadwalPelajaran::with(['mataPelajaran', 'guru'])->where('kelas_id', $siswa->kelas_id)->get(),
        ];

        return view('dashboard.siswa_jadwal', $data);
    }

    public function siswaPresensi()
    {
        $user = Auth::user();
        if (!$user->isSiswa()) {
            abort(403);
        }
        $siswa = $user->siswa;
        if (!$siswa) {
            abort(404, 'Detail siswa tidak ditemukan.');
        }

        $data = [
            'siswa' => $siswa,
            'kelas' => $siswa->kelas,
            'presensis' => Presensi::where('siswa_id', $siswa->id)->get(),
        ];

        return view('dashboard.siswa_presensi', $data);
    }

    public function adminUsersIndex()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $data = [
            'users' => User::with('role')->get(),
            'roles' => Role::all(),
        ];
        return view('dashboard.users', $data);
    }

    public function guruJadwalIndex()
    {
        $user = Auth::user();
        if (!$user->isGuru()) {
            abort(403);
        }
        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $data = [
            'guru' => $guru,
            'jadwals' => JadwalPelajaran::with(['kelas', 'mataPelajaran'])->where('guru_id', $guru->id)->get(),
            'kelas' => Kelas::all(),
            'mapels' => MataPelajaran::all(),
        ];
        return view('dashboard.guru_jadwal', $data);
    }

    public function guruElearningIndex()
    {
        $user = Auth::user();
        if (!$user->isGuru()) {
            abort(403);
        }
        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $data = [
            'guru' => $guru,
            'materiTugas' => MateriTugas::with(['kelas', 'mataPelajaran'])->where('guru_id', $guru->id)->get(),
            'kelas' => Kelas::all(),
            'mapels' => MataPelajaran::all(),
        ];
        return view('dashboard.guru_elearning', $data);
    }

    public function guruNilaiIndex()
    {
        $user = Auth::user();
        if (!$user->isGuru()) {
            abort(403);
        }
        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $data = [
            'guru' => $guru,
            'siswas' => Siswa::with('kelas')->get(),
            'kelas' => Kelas::all(),
            'mapels' => MataPelajaran::all(),
            'nilais' => Nilai::with(['siswa', 'mataPelajaran'])->latest()->take(20)->get(),
        ];
        return view('dashboard.guru_nilai', $data);
    }

    public function bkPelanggaranIndex()
    {
        $user = Auth::user();
        if (!$user->isGuruBk()) {
            abort(403);
        }
        $guru = $user->guru;

        $data = [
            'guru' => $guru,
            'siswas' => Siswa::with('kelas')->get(),
            'pelanggarans' => PelanggaranBk::with(['siswa', 'guruBk'])->get(),
            'gurus' => Guru::all(),
        ];
        return view('dashboard.bk_pelanggaran', $data);
    }

    public function guruCbtIndex()
    {
        $user = Auth::user();
        if (!$user->isGuru()) {
            abort(403);
        }
        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $data = [
            'guru' => $guru,
        ];
        return view('dashboard.guru_cbt', $data);
    }
}
