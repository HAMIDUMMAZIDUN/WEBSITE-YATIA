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
use App\Models\AttendanceDaily;
use App\Models\AuditLog;
use App\Models\SchoolProfile;
use App\Models\PromotionDecision;
use App\Models\AcademicYear;
use App\Models\AcademicHoliday;
use App\Models\ClassHomeroom;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->superAdminDashboard($request);
        } elseif ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index');
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

    private function superAdminDashboard(Request $request)
    {
        $selectedUnit = $request->input('unit', 'MA');
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $classes = Kelas::where('unit', $selectedUnit)->orderBy('nama_kelas', 'asc')->get();
        $allClassIds = $classes->pluck('id')->toArray();
        
        $selectedClassIds = $request->input('kelas_ids');
        if (empty($selectedClassIds)) {
            $selectedClassIds = $allClassIds;
        } else {
            $selectedClassIds = array_map('intval', (array) $selectedClassIds);
            $selectedClassIds = array_intersect($selectedClassIds, $allClassIds);
        }

        // Calculate attendance stats for filtered classes and date
        $totalSiswaCount = Siswa::whereIn('kelas_id', $selectedClassIds)->count();
        
        $attendanceRecords = AttendanceDaily::whereIn('class_id', $selectedClassIds)
            ->where('date', $date)
            ->get();

        $totalHadir = $attendanceRecords->whereIn('status', ['H', 'T', 'D'])->count();
        $totalSakit = $attendanceRecords->where('status', 'S')->count();
        $totalIzin = $attendanceRecords->where('status', 'I')->count();
        $totalAlpa = max(0, $totalSiswaCount - $totalHadir - $totalSakit - $totalIzin);
        
        $totalKehadiranPct = $totalSiswaCount > 0 ? ($totalHadir / $totalSiswaCount) * 100 : 0;

        $attendanceStats = [
            'total_siswa' => $totalSiswaCount,
            'hadir' => $totalHadir,
            'sakit' => $totalSakit,
            'izin' => $totalIzin,
            'alpa' => $totalAlpa,
            'kehadiran_pct' => $totalKehadiranPct,
        ];

        // Compile class status list & table data
        $tableData = [];
        $classesFilledCount = 0;

        foreach ($classes as $cls) {
            $cTotal = Siswa::where('kelas_id', $cls->id)->count();
            $cRecords = $attendanceRecords->where('class_id', $cls->id);
            
            $cHadir = $cRecords->whereIn('status', ['H', 'T', 'D'])->count();
            $cSakit = $cRecords->where('status', 'S')->count();
            $cIzin = $cRecords->where('status', 'I')->count();
            $cAlpa = max(0, $cTotal - $cHadir - $cSakit - $cIzin);
            $cPct = $cTotal > 0 ? ($cHadir / $cTotal) * 100 : 0;

            if ($cRecords->count() > 0) {
                $classesFilledCount++;
            }

            if (in_array($cls->id, $selectedClassIds)) {
                $tableData[] = [
                    'id' => $cls->id,
                    'nama_kelas' => $cls->nama_kelas,
                    'total_siswa' => $cTotal,
                    'hadir' => $cHadir,
                    'sakit' => $cSakit,
                    'izin' => $cIzin,
                    'alpa' => $cAlpa,
                    'kehadiran_pct' => $cPct,
                ];
            }
        }

        $totals = [
            'total_siswa' => $totalSiswaCount,
            'hadir' => $totalHadir,
            'sakit' => $totalSakit,
            'izin' => $totalIzin,
            'alpa' => $totalAlpa,
            'kehadiran_pct' => $totalKehadiranPct,
        ];

        // Handle Exports
        $export = $request->input('export');
        if ($export === 'excel') {
            return $this->exportRekapExcel($tableData, $totals, $selectedUnit, $date);
        } elseif ($export === 'pdf') {
            return view('dashboard.admin.sistem.rekap_print', [
                'tableData' => $tableData,
                'totals' => $totals,
                'selectedUnit' => $selectedUnit,
                'date' => $date,
            ]);
        }

        // Retrieve latest audit logs
        $latestActivities = AuditLog::with('user')->latest()->take(5)->get();

        $data = [
            'counts' => [
                'users' => User::count(),
                'gurus_l' => Guru::where('jenis_kelamin', 'L')->count(),
                'gurus_p' => Guru::where('jenis_kelamin', 'P')->count(),
                'gurus_total' => Guru::count(),
                'siswas_l' => Siswa::where('jenis_kelamin', 'L')->count(),
                'siswas_p' => Siswa::where('jenis_kelamin', 'P')->count(),
                'siswas_total' => Siswa::count(),
                'kelas_total' => Kelas::count(),
                'mapel_total' => MataPelajaran::count(),
            ],
            'attendance_stats' => $attendanceStats,
            'tableData' => $tableData,
            'totals' => $totals,
            'selectedUnit' => $selectedUnit,
            'selectedClassIds' => $selectedClassIds,
            'date' => $date,
            'classes_filled_count' => $classesFilledCount,
            'classes_total_count' => $classes->count(),
            'latest_activities' => $latestActivities,
            'users' => User::with('role')->get(),
            'gurus' => Guru::with('user')->get(),
            'siswas' => Siswa::with(['user', 'kelas'])->get(),
            'kelas' => $classes,
            'mapel' => MataPelajaran::all(),
            'roles' => Role::all(),
        ];
        return view('dashboard.superadmin.dashboard', $data);
    }

    private function exportRekapExcel($tableData, $totals, $selectedUnit, $date)
    {
        $unitLabel = $selectedUnit === 'MA' ? 'Aliyah' : 'MTS';
        $filename = 'rekap_kehadiran_' . strtolower($unitLabel) . '_' . str_replace('-', '', $date) . '.csv';
        
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($tableData, $totals, $unitLabel, $date) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            
            fputcsv($file, ['Rekapitulasi Kehadiran Harian per Kelas']);
            fputcsv($file, ['Unit', $unitLabel]);
            fputcsv($file, ['Tanggal', $date]);
            fputcsv($file, []); // Empty line
            
            fputcsv($file, ['Kelas', 'Total Siswa', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Kehadiran (%)']);
            
            foreach ($tableData as $row) {
                fputcsv($file, [
                    $row['nama_kelas'],
                    $row['total_siswa'],
                    $row['hadir'],
                    $row['sakit'],
                    $row['izin'],
                    $row['alpa'],
                    number_format($row['kehadiran_pct'], 1) . '%'
                ]);
            }
            
            fputcsv($file, [
                'TOTAL',
                $totals['total_siswa'],
                $totals['hadir'],
                $totals['sakit'],
                $totals['izin'],
                $totals['alpa'],
                number_format($totals['kehadiran_pct'], 1) . '%'
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
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
        return view('dashboard.guru.dashboard', $data);
    }

    private function waliKelasDashboard()
    {
        return redirect()->route('wali.dashboard');
    }

    private function siswaDashboard()
    {
        $user = Auth::user();

        // Check if the student is assigned as an attendance officer
        $assignment = \App\Models\AttendanceOfficerAssignment::where('user_id', $user->id)
            ->where('active', true)
            ->first();

        if ($assignment) {
            // Redirect to the attendance input page for their assigned class
            return redirect()->route('absensi.officer.index', $assignment->class_id);
        }

        // Fallback: generic empty page for unassigned students
        return view('dashboard.absensi_empty', [
            'message' => 'Anda belum ditugaskan sebagai petugas absensi. Silakan hubungi Wali Kelas Anda.',
        ]);
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
        return view('dashboard.guru_bk.dashboard', $data);
    }

    // ACTIONS FOR DATA MANAGEMENT

    // 1. Super Admin Actions
    public function storeUser(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'nik'  => 'nullable|string|max:100|unique:users,nik',
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
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        // Auto-generate NIK from email prefix if not provided
        $nik = $request->nik ?: preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $request->email)[0]);
        // Ensure NIK uniqueness
        $baseNik = $nik;
        $suffix = 1;
        while (\App\Models\User::where('nik', $nik)->exists()) {
            $nik = $baseNik . $suffix++;
        }

        // Determine username
        $username = $request->username ?: $nik;
        $baseUsername = $username;
        $uSuffix = 1;
        while (\App\Models\User::where('username', $username)->exists()) {
            $username = $baseUsername . $uSuffix++;
        }

        $user = User::create([
            'name' => $request->name,
            'nik'  => $nik,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role_id' => $request->role_id,
        ]);

        // If user is a guru, wali_kelas or guru_bk, automatically update placeholder Guru record
        $role = Role::find($request->role_id);
        if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'password_plain' => $request->password,
                ]
            );
        } elseif ($role->name === 'absensi') {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                ]
            );
        }

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function deleteUser($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus diri sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function storeKelas(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'unit' => 'required|in:MTs,MA,Semua',
        ]);

        Kelas::create($request->all());
        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function storeMapel(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajarans',
            'unit' => 'required|in:MTs,MA,Semua',
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
            } elseif ($type === 'guru') {
                fputcsv($file, ['Nama', 'Email', 'Hak Akses', 'Jenis Kelamin', 'NIK/NUPTK', 'Tempat Lahir', 'Tanggal Lahir', 'Pendidikan', 'JTM']);
                $gurus = Guru::with('user.role')->get();
                foreach ($gurus as $g) {
                    fputcsv($file, [
                        $g->nama,
                        $g->user->email ?? '-',
                        $g->user->role->name ?? '-',
                        $g->jenis_kelamin,
                        $g->nik_nuptk ?? '-',
                        $g->tempat_lahir ?? '-',
                        $g->tanggal_lahir ?? '-',
                        $g->pendidikan ?? '-',
                        $g->jtm ?? 0
                    ]);
                }
            } elseif ($type === 'siswa') {
                fputcsv($file, ['Nama', 'Email', 'NIS', 'NISN', 'Nama Kelas', 'Jenis Kelamin', 'No HP', 'Alamat']);
                $user = Auth::user();
                if ($user->isWaliKelas() && $user->guru) {
                    $activeYear = \App\Models\AcademicYear::where('active', true)->first();
                    $yearId = $activeYear ? $activeYear->id : null;
                    $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $user->guru->id)
                        ->where('active', true)
                        ->when($yearId, function($q) use ($yearId) {
                            return $q->where('academic_year_id', $yearId);
                        })
                        ->first();
                    $kelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', $user->guru->id)->first();
                    if ($kelas) {
                        $siswas = Siswa::with(['user', 'kelas'])->where('kelas_id', $kelas->id)->get();
                    } else {
                        $siswas = collect();
                    }
                } else {
                    $siswas = Siswa::with(['user', 'kelas'])->get();
                }
                foreach ($siswas as $s) {
                    fputcsv($file, [
                        $s->nama,
                        $s->user->email ?? '-',
                        $s->nis ?? '-',
                        $s->nisn ?? '-',
                        $s->kelas->nama_kelas ?? '-',
                        $s->jenis_kelamin,
                        $s->no_hp ?? '-',
                        $s->alamat ?? '-'
                    ]);
                }
            } elseif ($type === 'nilai') {
                fputcsv($file, ['NIS Siswa', 'Nama Siswa', 'Kode Mapel', 'Nama Mapel', 'Jenis Nilai', 'Nilai', 'Keterangan']);
                $query = Nilai::with(['siswa', 'mataPelajaran']);
                $user = Auth::user();
                if ($user->isGuru() && $user->guru) {
                    // Filter by class or mapel taught, but let's do simple export of all for simplicity/full access
                }
                $nilais = $query->get();
                foreach ($nilais as $n) {
                    fputcsv($file, [
                        $n->siswa->nis ?? '-',
                        $n->siswa->nama ?? '-',
                        $n->mataPelajaran->kode_mapel ?? '-',
                        $n->mataPelajaran->nama_mapel ?? '-',
                        $n->jenis_nilai,
                        $n->nilai,
                        $n->keterangan ?? '-'
                    ]);
                }
            } elseif ($type === 'jadwal') {
                fputcsv($file, ['Nama Kelas', 'Kode Mapel', 'Nama Mapel', 'Hari', 'Jam Mulai', 'Jam Selesai']);
                $query = JadwalPelajaran::with(['kelas', 'mataPelajaran']);
                $user = Auth::user();
                if ($user->isGuru() && $user->guru) {
                    $query->where('guru_id', $user->guru->id);
                }
                $jadwals = $query->get();
                foreach ($jadwals as $j) {
                    fputcsv($file, [
                        $j->kelas->nama_kelas ?? '-',
                        $j->mataPelajaran->kode_mapel ?? '-',
                        $j->mataPelajaran->nama_mapel ?? '-',
                        $j->hari,
                        $j->jam_mulai,
                        $j->jam_selesai
                    ]);
                }
            } elseif ($type === 'holidays') {
                fputcsv($file, ['Tahun Ajaran', 'Semester', 'Tanggal Mulai', 'Tanggal Selesai', 'Keterangan', 'Unit', 'Is Nasional']);
                $holidays = AcademicHoliday::with('academicYear')->get();
                foreach ($holidays as $h) {
                    fputcsv($file, [
                        $h->academicYear->tahun_ajaran ?? '-',
                        $h->academicYear->semester ?? '-',
                        $h->tanggal_mulai,
                        $h->tanggal_selesai,
                        $h->keterangan,
                        $h->unit,
                        $h->is_nasional ? 'Ya' : 'Tidak'
                    ]);
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
            } elseif ($type === 'academic_years') {
                fputcsv($file, ['Tahun Ajaran', 'Semester', 'Tanggal Mulai', 'Tanggal Selesai', 'Hari Sekolah', 'Unit', 'Aktif', 'Terkunci']);
                $years = AcademicYear::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'asc')->get();
                foreach ($years as $y) {
                    fputcsv($file, [
                        $y->tahun_ajaran,
                        $y->semester,
                        $y->tanggal_mulai,
                        $y->tanggal_selesai,
                        $y->hari_sekolah,
                        $y->unit,
                        $y->active ? 'Ya' : 'Tidak',
                        $y->locked ? 'Ya' : 'Tidak'
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
                fputcsv($file, ['Nama', 'Email', 'Password', 'Role (super_admin/guru/wali_kelas/siswa/guru_bk/wali_kelas_guru)', 'Jenis Kelamin (L/P)']);
                fputcsv($file, ['John Doe', 'johndoe@gmail.com', 'password123', 'siswa', 'L']);
                fputcsv($file, ['Jane Smith, M.Pd', 'janesmith@gmail.com', 'password123', 'guru', 'P']);
            } elseif ($type === 'guru') {
                fputcsv($file, ['Nama', 'Email', 'Password', 'Hak Akses (guru/wali_kelas/guru_bk/wali_kelas_guru)', 'Jenis Kelamin (L/P)', 'NIK/NUPTK', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 'Pendidikan (Sarjana (S1)/Magister (S2)/Diploma (D4)/Doktor (S3))', 'JTM']);
                fputcsv($file, ['Ahmad Ariyadi, S.Pd.I.', 'ahmad@gmail.com', 'password123', 'guru', 'L', '3204461404040002', 'Bandung', '1985-05-12', 'Sarjana (S1)', '24']);
            } elseif ($type === 'siswa') {
                fputcsv($file, ['Nama', 'Email', 'Password', 'NIS', 'NISN', 'Nama Kelas', 'Jenis Kelamin (L/P)', 'No HP', 'Alamat']);
                fputcsv($file, ['Budi Utomo', 'budi@gmail.com', 'siswa123', '10024', '0081234567', 'Kelas VII-A', 'L', '081234567890', 'Jl. Merdeka No. 10']);
            } elseif ($type === 'nilai') {
                fputcsv($file, ['NIS Siswa', 'Kode Mapel', 'Jenis Nilai (UH/UTS/UAS/Tugas)', 'Nilai', 'Keterangan']);
                fputcsv($file, ['10024', 'MAT-01', 'UH', '85', 'Kuis bab pecahan']);
            } elseif ($type === 'jadwal') {
                fputcsv($file, ['Nama Kelas', 'Kode Mapel', 'Hari (Senin/Selasa/Rabu/Kamis/Jumat/Sabtu)', 'Jam Mulai (HH:MM)', 'Jam Selesai (HH:MM)']);
                fputcsv($file, ['Kelas VII-A', 'MAT-01', 'Senin', '07:30', '09:00']);
            } elseif ($type === 'holidays') {
                fputcsv($file, ['Tahun Ajaran', 'Semester (Ganjil/Genap)', 'Tanggal Mulai (YYYY-MM-DD)', 'Tanggal Selesai (YYYY-MM-DD)', 'Keterangan', 'Unit (MTs/MA/Semua)', 'Is Nasional (Ya/Tidak)']);
                fputcsv($file, ['2026/2027', 'Ganjil', '2026-12-20', '2026-12-31', 'Libur Semester Ganjil', 'Semua', 'Ya']);
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
            } elseif ($type === 'academic_years') {
                fputcsv($file, ['Tahun Ajaran', 'Semester (Ganjil/Genap)', 'Tanggal Mulai (YYYY-MM-DD)', 'Tanggal Selesai (YYYY-MM-DD)', 'Hari Sekolah (comma-separated, e.g. Monday,Tuesday,Wednesday,Thursday,Friday,Saturday)', 'Unit (MTs/MA/Semua)']);
                fputcsv($file, ['2026/2027', 'Ganjil', '2026-07-15', '2026-12-20', 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday', 'Semua']);
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
                    $gender = isset($row[4]) ? strtoupper(trim($row[4])) : 'L';
                    if (!in_array($gender, ['L', 'P'])) {
                        $gender = 'L';
                    }

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
                        Guru::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'nama' => $user->name,
                                'jenis_kelamin' => $gender,
                            ]
                        );
                    } elseif ($role->name === 'absensi') {
                        $defaultKelas = Kelas::first();
                        Siswa::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'nama' => $user->name,
                                'kelas_id' => $defaultKelas ? $defaultKelas->id : null,
                                'jenis_kelamin' => $gender,
                            ]
                        );
                    }
                    $importedCount++;

                } elseif ($type === 'guru') {
                    if (count($row) < 5) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $password = trim($row[2]);
                    $roleName = trim($row[3]);
                    $gender = strtoupper(trim($row[4]));
                    $nik_nuptk = isset($row[5]) ? trim($row[5]) : null;
                    $tempat_lahir = isset($row[6]) ? trim($row[6]) : null;
                    $tanggal_lahir = isset($row[7]) ? trim($row[7]) : null;
                    $pendidikan = isset($row[8]) ? trim($row[8]) : null;
                    $jtm = isset($row[9]) ? intval(trim($row[9])) : 0;

                    if (!in_array($gender, ['L', 'P'])) {
                        $gender = 'L';
                    }

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

                    Guru::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama' => $user->name,
                            'jenis_kelamin' => $gender,
                            'nik_nuptk' => $nik_nuptk,
                            'tempat_lahir' => $tempat_lahir,
                            'tanggal_lahir' => $tanggal_lahir,
                            'pendidikan' => $pendidikan,
                            'jtm' => $jtm,
                        ]
                    );
                    $importedCount++;

                } elseif ($type === 'siswa') {
                    if (count($row) < 7) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $password = trim($row[2]);
                    $nis = trim($row[3]);
                    $nisn = trim($row[4]);
                    $namaKelas = trim($row[5]);
                    $gender = strtoupper(trim($row[6]));
                    $no_hp = isset($row[7]) ? trim($row[7]) : null;
                    $alamat = isset($row[8]) ? trim($row[8]) : null;

                    if (!in_array($gender, ['L', 'P'])) {
                        $gender = 'L';
                    }

                    if (User::where('email', $email)->exists()) {
                        throw new \Exception("Email '$email' sudah terdaftar (Baris $rowCount)");
                    }

                    if (Siswa::where('nis', $nis)->exists()) {
                        throw new \Exception("Siswa dengan NIS '$nis' sudah terdaftar (Baris $rowCount)");
                    }

                    $targetKelas = null;
                    if (Auth::user()->isWaliKelas() && Auth::user()->guru) {
                        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
                        $yearId = $activeYear ? $activeYear->id : null;
                        $homeroom = \App\Models\ClassHomeroom::where('teacher_id', Auth::user()->guru->id)
                            ->where('active', true)
                            ->when($yearId, function($q) use ($yearId) {
                                return $q->where('academic_year_id', $yearId);
                            })
                            ->first();
                        $waliKelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', Auth::user()->guru->id)->first();
                        if (!$waliKelas) {
                            throw new \Exception("Anda tidak memiliki kelas binaan aktif (Baris $rowCount)");
                        }

                        if (!empty($namaKelas)) {
                            $checkKelas = Kelas::where('nama_kelas', $namaKelas)->first();
                            if (!$checkKelas || $checkKelas->id !== $waliKelas->id) {
                                throw new \Exception("Anda hanya diperbolehkan mengimpor siswa ke kelas Anda sendiri '$waliKelas->nama_kelas' (Baris $rowCount)");
                            }
                        }
                        $targetKelas = $waliKelas;
                    } else {
                        $targetKelas = Kelas::where('nama_kelas', $namaKelas)->first();
                        if (!$targetKelas) {
                            throw new \Exception("Kelas '$namaKelas' tidak ditemukan (Baris $rowCount)");
                        }
                    }

                    $role = Role::where('name', 'absensi')->first();

                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role_id' => $role->id,
                        'is_active' => true,
                    ]);

                    Siswa::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama' => $user->name,
                            'nis' => $nis,
                            'nisn' => $nisn,
                            'kelas_id' => $targetKelas->id,
                            'jenis_kelamin' => $gender,
                            'no_hp' => $no_hp,
                            'alamat' => $alamat,
                            'status' => 'Aktif',
                        ]
                    );
                    $importedCount++;

                } elseif ($type === 'nilai') {
                    if (count($row) < 4) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $nis = trim($row[0]);
                    $kodeMapel = trim($row[1]);
                    $jenisNilai = trim($row[2]);
                    $nilaiVal = intval(trim($row[3]));
                    $keterangan = isset($row[4]) ? trim($row[4]) : null;

                    $siswa = Siswa::where('nis', $nis)->first();
                    if (!$siswa) {
                        throw new \Exception("Siswa dengan NIS '$nis' tidak ditemukan (Baris $rowCount)");
                    }

                    $mapel = MataPelajaran::where('kode_mapel', $kodeMapel)->first();
                    if (!$mapel) {
                        throw new \Exception("Mapel dengan Kode '$kodeMapel' tidak ditemukan (Baris $rowCount)");
                    }

                    Nilai::create([
                        'siswa_id' => $siswa->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'jenis_nilai' => $jenisNilai,
                        'nilai' => $nilaiVal,
                        'keterangan' => $keterangan,
                    ]);
                    $importedCount++;

                } elseif ($type === 'jadwal') {
                    if (count($row) < 5) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $namaKelas = trim($row[0]);
                    $kodeMapel = trim($row[1]);
                    $hari = trim($row[2]);
                    $jamMulai = trim($row[3]);
                    $jamSelesai = trim($row[4]);

                    $kelas = Kelas::where('nama_kelas', $namaKelas)->first();
                    if (!$kelas) {
                        throw new \Exception("Kelas '$namaKelas' tidak ditemukan (Baris $rowCount)");
                    }

                    $mapel = MataPelajaran::where('kode_mapel', $kodeMapel)->first();
                    if (!$mapel) {
                        throw new \Exception("Mapel dengan Kode '$kodeMapel' tidak ditemukan (Baris $rowCount)");
                    }

                    $guru = Auth::user()->guru;

                    JadwalPelajaran::create([
                        'guru_id' => $guru ? $guru->id : null,
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                    ]);
                    $importedCount++;

                } elseif ($type === 'holidays') {
                    if (count($row) < 5) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $tahunAjaran = trim($row[0]);
                    $semester = trim($row[1]);
                    $tanggalMulai = trim($row[2]);
                    $tanggalSelesai = trim($row[3]);
                    $keterangan = trim($row[4]);
                    $unit = isset($row[5]) ? trim($row[5]) : 'Semua';
                    $isNasional = isset($row[6]) && (strtolower(trim($row[6])) === 'ya' || trim($row[6]) === '1') ? 1 : 0;

                    $ay = AcademicYear::where('tahun_ajaran', $tahunAjaran)->where('semester', $semester)->first();
                    if (!$ay) {
                        throw new \Exception("Tahun Ajaran '$tahunAjaran' Semester '$semester' tidak ditemukan (Baris $rowCount)");
                    }

                    AcademicHoliday::create([
                        'academic_year_id' => $ay->id,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'keterangan' => $keterangan,
                        'unit' => $unit,
                        'is_nasional' => $isNasional,
                    ]);
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
                } elseif ($type === 'academic_years') {
                    if (count($row) < 6) {
                        throw new \Exception("Kolom tidak lengkap pada baris $rowCount");
                    }
                    $tahunAjaran = trim($row[0]);
                    $semester = trim($row[1]);
                    $tglMulai = trim($row[2]);
                    $tglSelesai = trim($row[3]);
                    $hariSekolah = trim($row[4]);
                    $unit = trim($row[5]);

                    $exists = AcademicYear::where('tahun_ajaran', $tahunAjaran)
                        ->where('semester', $semester)
                        ->where('unit', $unit)
                        ->exists();

                    if ($exists) {
                        throw new \Exception("Tahun ajaran '$tahunAjaran' semester '$semester' unit '$unit' sudah terdaftar (Baris $rowCount)");
                    }

                    AcademicYear::create([
                        'tahun_ajaran' => $tahunAjaran,
                        'semester' => $semester,
                        'tanggal_mulai' => $tglMulai,
                        'tanggal_selesai' => $tglSelesai,
                        'hari_sekolah' => $hariSekolah,
                        'unit' => $unit,
                        'active' => false,
                        'locked' => false,
                    ]);
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
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
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
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
            $userData['password_plain'] = $request->password;
        }

        $user->update($userData);

        // Update or create corresponding Guru or Siswa profile depending on new role
        $role = Role::find($request->role_id);
        if (in_array($role->name, ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])) {
            $guruData = [
                'nama' => $user->name,
                'jenis_kelamin' => $request->jenis_kelamin,
            ];
            if ($request->filled('password')) {
                $guruData['password_plain'] = $request->password;
            }
            Guru::updateOrCreate(['user_id' => $user->id], $guruData);
        } elseif ($role->name === 'absensi') {
            $defaultKelas = Kelas::first();
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name,
                    'kelas_id' => $user->siswa ? $user->siswa->kelas_id : ($defaultKelas ? $defaultKelas->id : null),
                    'jenis_kelamin' => $request->jenis_kelamin,
                ]
            );
        }

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    public function kelasIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $unit = $request->query('unit', 'MTs');
        $query = Kelas::with('waliGuru');
        if (in_array($unit, ['MTs', 'MA'])) {
            $query->where('unit', $unit);
        }

        $data = [
            'kelas' => $query->get(),
            'gurus' => Guru::all(),
            'selected_unit' => $unit,
        ];
        return view('dashboard.admin.master_data.kelas', $data);
    }

    public function updateKelas(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'unit' => 'required|in:MTs,MA,Semua',
        ]);

        $kelas->update($request->all());
        return redirect()->back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function mapelIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $unit = $request->query('unit', 'MTs');
        $query = MataPelajaran::query();
        if (in_array($unit, ['MTs', 'MA'])) {
            $query->whereIn('unit', [$unit, 'Semua']);
        }

        $data = [
            'mapel' => $query->get(),
            'selected_unit' => $unit,
        ];
        return view('dashboard.admin.master_data.mapel', $data);
    }

    public function updateMapel(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $mapel = MataPelajaran::findOrFail($id);
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajarans,kode_mapel,' . $mapel->id,
            'unit' => 'required|in:MTs,MA,Semua',
        ]);

        $mapel->update($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function deleteMapel($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
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

        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;
        $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $guru->id)
            ->where('active', true)
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->first();

        $kelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', $guru->id)->first();

        $siswas = $kelas ? Siswa::where('kelas_id', $kelas->id)->get() : collect();
        $siswaIds = $siswas->pluck('id');

        // All students that can be assigned (not currently in this class)
        $availableStudents = Siswa::where(function($q) use ($kelas) {
            if ($kelas) {
                $q->where('kelas_id', '!=', $kelas->id)->orWhereNull('kelas_id');
            }
        })->orderBy('nama', 'asc')->get();

        $data = [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'nilais' => Nilai::whereIn('siswa_id', $siswaIds)->get(),
            'availableStudents' => $availableStudents,
        ];

        return view('dashboard.wali_kelas.data_siswa', $data);
    }

    public function waliSiswaStore(Request $request)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $guru = $user->guru;
        if (!$guru) {
            abort(404, 'Detail guru tidak ditemukan.');
        }

        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;
        $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $guru->id)
            ->where('active', true)
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->first();

        $kelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', $guru->id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda tidak ditugaskan sebagai Wali Kelas di kelas manapun.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nis' => 'required|string|unique:siswas',
            'nisn' => 'nullable|string|unique:siswas',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $siswaRole = Role::where('name', 'absensi')->firstOrFail();

        $usr = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role_id' => $siswaRole->id,
            'is_active' => true,
        ]);

        $siswa = Siswa::updateOrCreate(
            ['user_id' => $usr->id],
            [
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nama' => $request->name,
                'kelas_id' => $kelas->id,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'status' => 'Aktif',
            ]
        );

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'wali_create_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'new_value' => json_encode($siswa->toArray()),
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas Anda.');
    }

    public function waliSiswaAssign(Request $request)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $guru = $user->guru;
        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;
        $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $guru->id)
            ->where('active', true)
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->first();

        $kelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', $guru->id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda tidak ditugaskan sebagai Wali Kelas di kelas manapun.');
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $oldKelasId = $siswa->kelas_id;

        $siswa->update(['kelas_id' => $kelas->id]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'wali_assign_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'old_value' => json_encode(['kelas_id' => $oldKelasId]),
            'new_value' => json_encode(['kelas_id' => $kelas->id]),
        ]);

        return redirect()->back()->with('success', "Siswa {$siswa->nama} berhasil dipindahkan ke kelas Anda.");
    }

    public function waliSiswaRemove($id)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $guru = $user->guru;
        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;
        $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $guru->id)
            ->where('active', true)
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->first();

        $kelas = $homeroom ? $homeroom->class : Kelas::where('wali_kelas_id', $guru->id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda tidak ditugaskan sebagai Wali Kelas di kelas manapun.');
        }

        $siswa = Siswa::where('id', $id)->where('kelas_id', $kelas->id)->firstOrFail();
        $siswa->update(['kelas_id' => null]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'wali_remove_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'old_value' => json_encode(['kelas_id' => $kelas->id]),
            'new_value' => json_encode(['kelas_id' => null]),
        ]);

        return redirect()->back()->with('success', "Siswa {$siswa->nama} berhasil dikeluarkan dari kelas Anda.");
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

        return view('dashboard.wali_kelas.absensi.index', $data);
    }

    // =============================================================
    // ADMIN ABSENSI MANAGEMENT
    // =============================================================

    public function adminAbsensiIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $activeYear = \App\Models\AcademicYear::where('active', true)->first();

        // All officer assignments with relations
        $assignments = \App\Models\AttendanceOfficerAssignment::with(['user.siswa', 'class', 'academicYear'])
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('class_id')
            ->orderByDesc('active')
            ->get();

        // All classes for the assign form
        $kelasAll = Kelas::orderBy('nama_kelas')->get();

        // All users with siswa role who have a siswa record
        $siswaUsers = User::with(['siswa.kelas', 'role'])
            ->whereHas('role', fn($q) => $q->where('name', 'absensi'))
            ->whereHas('siswa')
            ->orderBy('name')
            ->get();

        // Attendance summary per class for today
        $today = Carbon::today()->toDateString();
        $submissionsByClass = \App\Models\AttendanceSubmission::with('submittedBy')
            ->where('date', $today)
            ->get()
            ->keyBy('class_id');

        $classSummary = $kelasAll->map(function($kelas) use ($today, $submissionsByClass, $assignments, $activeYear) {
            $totalSiswa = Siswa::where('kelas_id', $kelas->id)->count();
            $filled = \App\Models\AttendanceDaily::where('class_id', $kelas->id)->where('date', $today)->count();
            $submission = $submissionsByClass->get($kelas->id);
            $officers = $assignments->where('class_id', $kelas->id)->where('active', true);
            return [
                'kelas'      => $kelas,
                'total'      => $totalSiswa,
                'filled'     => $filled,
                'submission' => $submission,
                'officers'   => $officers,
            ];
        });

        return view('dashboard.admin.kesiswaan.absensi', [
            'assignments'   => $assignments,
            'kelasAll'      => $kelasAll,
            'siswaUsers'    => $siswaUsers,
            'classSummary'  => $classSummary,
            'activeYear'    => $activeYear,
            'today'         => $today,
        ]);
    }

    public function adminAbsensiAssign(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'class_id' => 'required|exists:kelas,id',
        ]);

        $activeYear = \App\Models\AcademicYear::where('active', true)->first();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.');
        }

        // Check if already exists
        $existing = \App\Models\AttendanceOfficerAssignment::where('user_id', $request->user_id)
            ->where('class_id', $request->class_id)
            ->where('academic_year_id', $activeYear->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Siswa tersebut sudah pernah ditugaskan untuk kelas ini. Gunakan tombol Toggle untuk mengaktifkan kembali.');
        }

        \App\Models\AttendanceOfficerAssignment::create([
            'user_id'          => $request->user_id,
            'class_id'         => $request->class_id,
            'academic_year_id' => $activeYear->id,
            'active'           => true,
        ]);

        $targetUser = User::find($request->user_id);
        $targetKelas = Kelas::find($request->class_id);
        return redirect()->back()->with('success', ($targetUser->name ?? 'Siswa') . ' berhasil ditugaskan sebagai petugas absensi kelas ' . ($targetKelas->nama_kelas ?? '') . '.');
    }

    public function adminAbsensiRevoke($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $assignment = \App\Models\AttendanceOfficerAssignment::findOrFail($id);
        $assignment->delete();

        return redirect()->back()->with('success', 'Penugasan petugas absensi berhasil dihapus.');
    }

    public function adminAbsensiToggle($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $assignment = \App\Models\AttendanceOfficerAssignment::findOrFail($id);
        $assignment->update(['active' => !$assignment->active]);
        $status = $assignment->active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', 'Penugasan petugas absensi berhasil ' . $status . '.');
    }

    public function adminUsersIndex()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403);
        }

        $data = [
            'users' => User::with('role')->get(),
            'roles' => Role::all(),
        ];
        return view('dashboard.admin.sistem.users', $data);
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
        return view('dashboard.guru.jadwal_jurnal', $data);
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
        return view('dashboard.guru.materi_tugas', $data);
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
        return view('dashboard.guru.asesmen_nilai', $data);
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
        return view('dashboard.guru_bk.program_bk', $data);
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
        return view('dashboard.guru.cbt', $data);
    }

    public function profilMadrasahIndex()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $profile = SchoolProfile::firstOrCreate(['id' => 1]);

        return view('dashboard.admin.profil_madrasah', compact('profile'));
    }

    public function updateProfilMadrasah(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $profile = SchoolProfile::firstOrCreate(['id' => 1]);

        $request->validate([
            'nama_yayasan' => 'nullable|string|max:255',
            'nama_mts' => 'nullable|string|max:255',
            'nama_ma' => 'nullable|string|max:255',
            'nsm_mts' => 'nullable|numeric',
            'nsm_ma' => 'nullable|numeric',
            'npsn_mts' => 'nullable|numeric',
            'npsn_ma' => 'nullable|numeric',
            'status_sekolah' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'pimpinan_mts' => 'nullable|string|max:255',
            'pimpinan_ma' => 'nullable|string|max:255',
            'nip_pimpinan_mts' => 'nullable|string|max:50',
            'nip_pimpinan_ma' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
            'stempel' => 'nullable|image|max:2048',
            'tanda_tangan' => 'nullable|image|max:2048',
            'alasan_perubahan' => 'required|string|min:5',
        ]);

        $oldData = $profile->toArray();
        $data = $request->except(['logo', 'stempel', 'tanda_tangan', 'alasan_perubahan']);

        // Handle File Uploads
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('school_assets', 'public');
            $data['logo_path'] = '/storage/' . $logoPath;
        }
        if ($request->hasFile('stempel')) {
            $stempelPath = $request->file('stempel')->store('school_assets', 'public');
            $data['stempel_path'] = '/storage/' . $stempelPath;
        }
        if ($request->hasFile('tanda_tangan')) {
            $tandaTanganPath = $request->file('tanda_tangan')->store('school_assets', 'public');
            $data['tanda_tangan_path'] = '/storage/' . $tandaTanganPath;
        }

        $profile->update($data);

        // Record Audit Log (FR-PROF-004)
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_school_profile',
            'entity' => 'school_profiles',
            'entity_id' => $profile->id,
            'old_value' => json_encode($oldData),
            'new_value' => json_encode($profile->fresh()->toArray()),
        ]);

        return redirect()->back()->with('success', 'Profil Madrasah berhasil diperbarui.');
    }

    public function academicYearsIndex()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $years = AcademicYear::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'asc')->get();
        return view('dashboard.admin.pengaturan.tahun_ajaran', compact('years'));
    }

    public function storeAcademicYear(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'tahun_ajaran' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'hari_sekolah' => 'required|array',
            'unit' => 'required|in:MTs,MA,Semua',
        ]);

        $exists = AcademicYear::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->where('unit', $request->unit)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tahun ajaran, semester, dan unit tersebut sudah terdaftar.');
        }

        $year = AcademicYear::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'hari_sekolah' => implode(',', $request->hari_sekolah),
            'unit' => $request->unit,
            'active' => false,
            'locked' => false,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'create_academic_year',
            'entity' => 'academic_years',
            'entity_id' => $year->id,
            'new_value' => json_encode($year->toArray()),
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran & Semester berhasil ditambahkan.');
    }

    public function activateAcademicYear(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $year = AcademicYear::findOrFail($id);
        $unit = $year->unit;

        DB::beginTransaction();
        try {
            // Deactivate overlapping active years
            $oldActivesQuery = AcademicYear::where('active', true);
            if ($unit !== 'Semua') {
                $oldActivesQuery->where(function($q) use ($unit) {
                    $q->where('unit', 'Semua')->orWhere('unit', $unit);
                });
            }
            $oldActives = $oldActivesQuery->get();

            foreach ($oldActives as $oa) {
                $oa->update(['active' => false]);
            }

            $year->update(['active' => true]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'activate_academic_year',
                'entity' => 'academic_years',
                'entity_id' => $year->id,
                'old_value' => json_encode($oldActives->pluck('id')->toArray()),
                'new_value' => json_encode($year->fresh()->toArray()),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Ajaran & Semester (' . $year->unit . ') berhasil diaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengaktifkan periode akademik: ' . $e->getMessage());
        }
    }

    public function lockAcademicYear(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $year = AcademicYear::findOrFail($id);
        $year->update(['locked' => true]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'lock_academic_year',
            'entity' => 'academic_years',
            'entity_id' => $year->id,
            'new_value' => json_encode(['locked' => true]),
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran/Semester berhasil dikunci.');
    }

    public function unlockAcademicYear(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'alasan_perubahan' => 'required|string|max:500',
        ]);

        $year = AcademicYear::findOrFail($id);
        $year->update(['locked' => false]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'unlock_academic_year',
            'entity' => 'academic_years',
            'entity_id' => $year->id,
            'old_value' => json_encode(['locked' => true]),
            'new_value' => json_encode(['locked' => false, 'alasan' => $request->alasan_perubahan]),
        ]);

        return redirect()->back()->with('success', 'Kunci tahun ajaran/semester berhasil dibuka.');
    }

    public function copyAcademicYearConfig(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
            'copy_homerooms' => 'nullable|boolean',
            'copy_holidays' => 'nullable|boolean',
        ]);

        $fromYear = AcademicYear::findOrFail($request->from_year_id);
        $toYear = AcademicYear::findOrFail($id);

        if ($toYear->locked) {
            return redirect()->back()->with('error', 'Tahun ajaran tujuan sedang dikunci.');
        }

        DB::beginTransaction();
        try {
            if ($request->copy_homerooms) {
                $homerooms = ClassHomeroom::where('academic_year_id', $fromYear->id)->get();
                foreach ($homerooms as $hr) {
                    ClassHomeroom::firstOrCreate([
                        'academic_year_id' => $toYear->id,
                        'class_id' => $hr->class_id,
                        'teacher_id' => $hr->teacher_id,
                    ], [
                        'active' => $hr->active,
                    ]);
                }
            }

            if ($request->copy_holidays) {
                $holidays = AcademicHoliday::where('academic_year_id', $fromYear->id)->get();
                foreach ($holidays as $hol) {
                    AcademicHoliday::create([
                        'academic_year_id' => $toYear->id,
                        'tanggal_mulai' => $hol->tanggal_mulai,
                        'tanggal_selesai' => $hol->tanggal_selesai,
                        'keterangan' => $hol->keterangan,
                        'unit' => $hol->unit,
                        'is_nasional' => $hol->is_nasional,
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'copy_academic_year_config',
                'entity' => 'academic_years',
                'entity_id' => $toYear->id,
                'old_value' => json_encode(['from_year_id' => $fromYear->id]),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Konfigurasi berhasil disalin dari periode sebelumnya.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyalin konfigurasi: ' . $e->getMessage());
        }
    }

    public function holidaysIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $holidays = AcademicHoliday::with('academicYear')->orderBy('tanggal_mulai', 'desc')->get();
        $years = AcademicYear::orderBy('tahun_ajaran', 'desc')->get();
        $activeYear = AcademicYear::where('active', true)->first();

        return view('dashboard.admin.pengaturan.tahun_ajaran_libur', compact('holidays', 'years', 'activeYear'));
    }

    public function storeHoliday(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:255',
            'unit' => 'required|in:MTs,MA,Semua',
            'is_nasional' => 'nullable|boolean',
        ]);

        $academicYearId = $request->academic_year_id;
        $tMulai = Carbon::parse($request->tanggal_mulai)->toDateString();
        $tSelesai = Carbon::parse($request->tanggal_selesai)->toDateString();
        $unit = $request->unit;

        // Check range overlap
        $overlap = AcademicHoliday::where('academic_year_id', $academicYearId)
            ->where(function($q) use ($tMulai, $tSelesai) {
                $q->whereBetween('tanggal_mulai', [$tMulai, $tSelesai])
                  ->orWhereBetween('tanggal_selesai', [$tMulai, $tSelesai])
                  ->orWhere(function($sub) use ($tMulai, $tSelesai) {
                      $sub->where('tanggal_mulai', '<=', $tMulai)
                          ->where('tanggal_selesai', '>=', $tSelesai);
                  });
            })
            ->where(function($q) use ($unit) {
                if ($unit !== 'Semua') {
                    $q->whereIn('unit', [$unit, 'Semua']);
                }
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Tanggal libur tersebut tumpang tindih dengan libur lain yang sudah terdaftar.');
        }

        $holiday = AcademicHoliday::create([
            'academic_year_id' => $academicYearId,
            'tanggal_mulai' => $tMulai,
            'tanggal_selesai' => $tSelesai,
            'keterangan' => $request->keterangan,
            'unit' => $unit,
            'is_nasional' => $request->is_nasional ? true : false,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'create_holiday',
            'entity' => 'academic_holidays',
            'entity_id' => $holiday->id,
            'new_value' => json_encode($holiday->toArray()),
        ]);

        return redirect()->back()->with('success', 'Hari libur baru berhasil ditambahkan.');
    }

    public function deleteHoliday($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $holiday = AcademicHoliday::findOrFail($id);
        
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'delete_holiday',
            'entity' => 'academic_holidays',
            'entity_id' => $holiday->id,
            'old_value' => json_encode($holiday->toArray()),
        ]);

        $holiday->delete();

        return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
    }

    public function siswaIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $unit = $request->query('unit', 'MTs');

        $query = Siswa::with(['kelas', 'user']);

        if (in_array($unit, ['MTs', 'MA'])) {
            $query->whereHas('kelas', function($q) use ($unit) {
                $q->where('unit', $unit);
            });
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Class Filter
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'Aktif');
        }

        $siswas = $query->paginate(20)->withQueryString();
        $kelas = Kelas::where('unit', $unit)->get();

        return view('dashboard.admin.master_data.siswa', compact('siswas', 'kelas', 'unit'));
    }

    public function storeSiswa(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nis' => 'required|string|unique:siswas',
            'nisn' => 'nullable|string|unique:siswas',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $siswaRole = Role::where('name', 'absensi')->firstOrFail();

        $usr = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role_id' => $siswaRole->id,
        ]);

        $siswa = Siswa::updateOrCreate(
            ['user_id' => $usr->id],
            [
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nama' => $request->name,
                'kelas_id' => $request->kelas_id,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'status' => 'Aktif',
            ]
        );

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'create_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'new_value' => json_encode($siswa->toArray()),
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function updateSiswa(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $siswa = Siswa::findOrFail($id);
        $usr = $siswa->user;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usr->id,
            'password' => 'nullable|string|min:8',
            'nis' => 'required|string|unique:siswas,nis,' . $siswa->id,
            'nisn' => 'nullable|string|unique:siswas,nisn,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Mutasi,Keluar,Lulus,Drop Out',
        ]);

        $oldData = $siswa->toArray();

        $usr->update([
            'name' => $request->nama,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $usr->update([
                'password' => Hash::make($request->password),
                'password_plain' => $request->password,
            ]);
        }

        $siswa->update([
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'status' => $request->status,
        ]);

        // Sync users.is_active based on siswa status
        $isActive = $request->status === 'Aktif';
        $usr->update(['is_active' => $isActive]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'old_value' => json_encode($oldData),
            'new_value' => json_encode($siswa->fresh()->toArray()),
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function deleteSiswa($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $siswa = Siswa::findOrFail($id);
        $usr = $siswa->user;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'delete_siswa',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'old_value' => json_encode($siswa->toArray()),
        ]);

        $siswa->delete();
        if ($usr) {
            $usr->delete();
        }

        return redirect()->back()->with('success', 'Siswa berhasil dihapus secara permanen.');
    }

    public function siswaHistory($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $siswa = Siswa::with(['kelas', 'presensis', 'pelanggaranBks', 'promotionDecisions'])->findOrFail($id);

        // Compile timeline events chronologically
        $timeline = [];

        // 1. Enrollment/Creation event
        $timeline[] = [
            'date' => $siswa->created_at,
            'title' => 'Pendaftaran Siswa',
            'description' => "Siswa didaftarkan ke SIMS YATIA dengan NIS {$siswa->nis} di kelas " . ($siswa->kelas->nama_kelas ?? 'Belum Ditentukan'),
            'type' => 'creation',
            'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'
        ];

        // 2. Class promotion/history events
        foreach ($siswa->promotionDecisions as $decision) {
            $source = Kelas::find($decision->source_class);
            $target = Kelas::find($decision->target_class);
            $timeline[] = [
                'date' => $decision->created_at,
                'title' => 'Mutasi/Kenaikan Kelas (' . ucfirst($decision->decision) . ')',
                'description' => "Perubahan kelas dari " . ($source->nama_kelas ?? 'N/A') . " ke " . ($target->nama_kelas ?? 'N/A') . ". Catatan: " . ($decision->recommendation ?? '-'),
                'type' => 'promotion',
                'icon' => 'M9 5l7 7-7 7'
            ];
        }

        // 3. Counseling infractions events
        foreach ($siswa->pelanggaranBks as $infraction) {
            $timeline[] = [
                'date' => Carbon::parse($infraction->tanggal),
                'title' => 'Catatan Kasus BK',
                'description' => "Mencatat pelanggaran: {$infraction->pelanggaran} (+{$infraction->poin} poin). Tindakan: " . ($infraction->tindakan ?? '-'),
                'type' => 'bk',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
            ];
        }

        // 4. Attendance issues
        $absences = $siswa->presensis()->where('status', '!=', 'hadir')->get();
        foreach ($absences as $abs) {
            $timeline[] = [
                'date' => Carbon::parse($abs->tanggal),
                'title' => 'Ketidakhadiran Dicatat',
                'description' => "Ketidakhadiran terdeteksi: " . strtoupper($abs->status),
                'type' => 'attendance',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
            ];
        }

        // Sort timeline descending by date
        usort($timeline, function($a, $b) {
            return Carbon::parse($b['date'])->timestamp <=> Carbon::parse($a['date'])->timestamp;
        });

        return view('dashboard.admin.kesiswaan.riwayat', compact('siswa', 'timeline'));
    }

    public function pindahKelasIndex()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $kelas = Kelas::all();
        $siswas = Siswa::where('status', 'Aktif')->orderBy('nama')->get();

        return view('dashboard.admin.kesiswaan.pindah_kelas', compact('kelas', 'siswas'));
    }

    public function pindahKelasStore(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
            'source_class_id' => 'required|exists:kelas,id',
            'target_class_id' => 'required|exists:kelas,id|different:source_class_id',
            'catatan' => 'nullable|string',
        ]);

        $siswaIds = $request->siswa_ids;
        $sourceClassId = $request->source_class_id;
        $targetClassId = $request->target_class_id;

        // Atomic multi-move
        foreach ($siswaIds as $id) {
            $siswa = Siswa::findOrFail($id);
            if ($siswa->kelas_id == $sourceClassId) {
                // Log the decision
                PromotionDecision::create([
                    'student_id' => $siswa->id,
                    'source_class' => $sourceClassId,
                    'target_class' => $targetClassId,
                    'recommendation' => $request->catatan ?? 'Pindah kelas operasional manual.',
                    'decision' => 'tinggal', // represents horizontal class transfer
                ]);

                // Update class
                $siswa->update(['kelas_id' => $targetClassId]);

                // Audit Log
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'transfer_siswa_class',
                    'entity' => 'siswas',
                    'entity_id' => $siswa->id,
                    'old_value' => $sourceClassId,
                    'new_value' => $targetClassId,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Siswa terpilih berhasil dipindahkan ke kelas baru secara massal.');
    }

    public function naikKelasIndex()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $kelas = Kelas::withCount(['siswas' => function($q) {
            $q->where('status', 'Aktif');
        }])->get();

        return view('dashboard.admin.kesiswaan.naik_kelas', compact('kelas'));
    }

    public function naikKelasStore(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'source_class_id' => 'required|exists:kelas,id',
            'target_class_id' => 'required_if:action,promoted|nullable|exists:kelas,id|different:source_class_id',
            'action' => 'required|in:promoted,graduated',
            'catatan' => 'nullable|string',
        ]);

        $sourceClassId = $request->source_class_id;
        $action = $request->action;
        $targetClassId = $request->target_class_id;

        $siswas = Siswa::where('kelas_id', $sourceClassId)->where('status', 'Aktif')->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa aktif di kelas asal.');
        }

        foreach ($siswas as $siswa) {
            if ($action === 'promoted') {
                // Create Promotion log
                PromotionDecision::create([
                    'student_id' => $siswa->id,
                    'source_class' => $sourceClassId,
                    'target_class' => $targetClassId,
                    'recommendation' => $request->catatan ?? 'Kenaikan kelas akhir tahun ajaran.',
                    'decision' => 'naik',
                ]);

                // Move class
                $siswa->update(['kelas_id' => $targetClassId]);
            } else {
                // Graduate
                PromotionDecision::create([
                    'student_id' => $siswa->id,
                    'source_class' => $sourceClassId,
                    'target_class' => null,
                    'recommendation' => $request->catatan ?? 'Kelulusan siswa dari madrasah.',
                    'decision' => 'lulus',
                ]);

                // Set status and detach class
                $siswa->update([
                    'status' => 'Lulus',
                    'kelas_id' => null
                ]);
            }

            // Audit Log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => $action === 'promoted' ? 'mass_promotion' : 'mass_graduation',
                'entity' => 'siswas',
                'entity_id' => $siswa->id,
                'old_value' => $sourceClassId,
                'new_value' => $action === 'promoted' ? $targetClassId : 'Lulus',
            ]);
        }

        return redirect()->back()->with('success', count($siswas) . ' siswa berhasil diproses (' . ($action === 'promoted' ? 'Kenaikan Kelas' : 'Kelulusan') . ') secara massal.');
    }

    public function alumniIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $query = Siswa::whereIn('status', ['Lulus', 'Keluar', 'Drop Out']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $alumni = $query->paginate(20)->withQueryString();

        return view('dashboard.admin.kesiswaan.alumni', compact('alumni'));
    }

    public function updateAlumniContact(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $siswa = Siswa::findOrFail($id);
        $request->validate([
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
        ]);

        $oldData = $siswa->toArray();
        $siswa->update($request->only(['no_hp', 'alamat']));

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_alumni_contact',
            'entity' => 'siswas',
            'entity_id' => $siswa->id,
            'old_value' => json_encode($oldData),
            'new_value' => json_encode($siswa->fresh()->toArray()),
        ]);

        return redirect()->back()->with('success', 'Kontak alumni berhasil diperbarui.');
    }

    // ─── ADMIN: DATA GURU CRUD ──────────────────────────────────────────────
    public function adminGuruIndex()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $data = [
            'gurus' => Guru::with(['user', 'kelasWali'])->get(),
            'roles' => Role::whereIn('name', ['guru', 'wali_kelas', 'guru_bk', 'wali_kelas_guru'])->get(),
            'classes' => Kelas::all(),
        ];
        return view('dashboard.admin.master_data.guru', $data);
    }

    public function storeGuru(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
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
                }
            ],
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'jenis_kelamin' => 'required|in:L,P',
            'nip' => 'nullable|string|max:50|unique:gurus,nip',
            'nik_nuptk' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:255',
            'jtm' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'role_id' => $request->role_id,
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = '/storage/' . $request->file('foto')->store('teachers', 'public');
        }

        Guru::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => $request->filled('nip') ? $request->nip : 'NIP-' . time(),
                'nama' => $user->name,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nik_nuptk' => $request->nik_nuptk,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'pendidikan' => $request->pendidikan,
                'password_plain' => $request->password,
                'jtm' => $request->jtm ?? 0,
                'foto' => $fotoPath,
                'is_aktif' => true,
            ]
        );

        return redirect()->back()->with('success', 'Data Guru berhasil ditambahkan.');
    }

    public function updateGuru(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $guru = Guru::findOrFail($id);
        $user = $guru->user;

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
                }
            ],
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'jenis_kelamin' => 'required|in:L,P',
            'nip' => 'nullable|string|max:50|unique:gurus,nip,' . $guru->id,
            'nik_nuptk' => 'nullable|string|max:50',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:255',
            'jtm' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
            $userData['password_plain'] = $request->password;
        }

        $user->update($userData);

        $fotoPath = $guru->foto;
        if ($request->hasFile('foto')) {
            $fotoPath = '/storage/' . $request->file('foto')->store('teachers', 'public');
        }

        $guru->update([
            'nama' => $request->name,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nip' => $request->filled('nip') ? $request->nip : $guru->nip,
            'nik_nuptk' => $request->nik_nuptk,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'pendidikan' => $request->pendidikan,
            'password_plain' => $request->filled('password') ? $request->password : $guru->password_plain,
            'jtm' => $request->jtm ?? 0,
            'foto' => $fotoPath,
        ]);

        return redirect()->back()->with('success', 'Data Guru berhasil diperbarui.');
    }

    public function deleteGuru($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $guru = Guru::findOrFail($id);
        $user = $guru->user;
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus diri sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'Data Guru berhasil dihapus.');
    }

    public function toggleGuruStatus($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }
        $guru = Guru::findOrFail($id);
        $guru->is_aktif = !$guru->is_aktif;
        $guru->save();

        // Sync users.is_active so login is blocked/unblocked immediately
        if ($guru->user_id) {
            User::where('id', $guru->user_id)->update(['is_active' => $guru->is_aktif]);
        }

        $statusStr = $guru->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Guru {$guru->nama} berhasil {$statusStr}.");
    }

    public function toggleUserStatus($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            abort(403);
        }
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        // Sync related guru or siswa profile status
        if ($user->guru) {
            $user->guru->update(['is_aktif' => $user->is_active]);
        }
        if ($user->siswa) {
            $user->siswa->update([
                'status' => $user->is_active ? 'Aktif' : 'Keluar',
            ]);
        }

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusStr}.");
    }
}
