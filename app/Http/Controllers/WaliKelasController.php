<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AttendanceDaily;
use App\Models\AttendanceSubmission;
use App\Models\AttendanceVerification;
use App\Models\AttendanceAuditLog;
use App\Models\AcademicSnapshot;
use App\Models\StudentIntervention;
use App\Models\BkReferral;
use App\Models\HomeroomJournal;
use App\Models\ParentCommunication;
use App\Models\StudentGuardian;
use App\Models\ClassHomeroom;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaliKelasController extends Controller
{
    /**
     * Get the active class assigned to the current Wali Kelas.
     */
    protected function getActiveHomeroom()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            abort(403, 'Akses ditolak. Anda tidak terdaftar sebagai Guru.');
        }

        $activeYear = AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;

        $homeroom = ClassHomeroom::with('class')
            ->where('teacher_id', $guru->id)
            ->where('active', true)
            ->when($yearId, function($query) use ($yearId) {
                return $query->where('academic_year_id', $yearId);
            })
            ->first();

        return $homeroom;
    }

    /**
     * Wali Kelas Dashboard.
     */
    public function index()
    {
        $homeroom = $this->getActiveHomeroom();
        if (!$homeroom) {
            return view('dashboard.wali_kelas.wali_kelas_empty', [
                'message' => 'Anda saat ini tidak ditugaskan sebagai Wali Kelas pada tahun ajaran aktif.'
            ]);
        }

        $kelas = $homeroom->class;

        // Eager load students with related statistics to avoid N+1
        $siswas = Siswa::with([
            'user',
            'attendanceDaily' => function($q) {
                $q->where('date', Carbon::today()->toDateString());
            }
        ])
        ->where('kelas_id', $kelas->id)
        ->get();

        $siswaIds = $siswas->pluck('id');

        // Fetch counts for snapshots, violations, and attendance history
        $scoresBelowKktp = AcademicSnapshot::whereIn('student_id', $siswaIds)
            ->where('kktp_status', 'tidak_tuntas')
            ->get()
            ->groupBy('student_id');

        $violations = DB::table('achievements_violations')
            ->whereIn('student_id', $siswaIds)
            ->where('type', 'pelanggaran')
            ->select('student_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('student_id')
            ->get()
            ->pluck('total_points', 'student_id');

        $alpaCounts = AttendanceDaily::whereIn('student_id', $siswaIds)
            ->where('status', 'A')
            ->select('student_id', DB::raw('COUNT(*) as count'))
            ->groupBy('student_id')
            ->get()
            ->pluck('count', 'student_id');

        // Identify "Siswa Prioritas" based on multiple indicators
        $priorityStudents = [];
        foreach ($siswas as $siswa) {
            $hasAcademicIssue = isset($scoresBelowKktp[$siswa->id]) && count($scoresBelowKktp[$siswa->id]) > 0;
            $hasDisciplineIssue = ($violations[$siswa->id] ?? 0) >= 50;
            $hasAttendanceIssue = ($alpaCounts[$siswa->id] ?? 0) >= 3;

            if ($hasAcademicIssue || $hasDisciplineIssue || $hasAttendanceIssue) {
                $priorityStudents[] = [
                    'siswa' => $siswa,
                    'academic_issue' => $hasAcademicIssue ? count($scoresBelowKktp[$siswa->id]) . ' Mapel di bawah KKTP' : null,
                    'discipline_issue' => $hasDisciplineIssue ? 'Poin Pelanggaran: ' . $violations[$siswa->id] : null,
                    'attendance_issue' => $hasAttendanceIssue ? $alpaCounts[$siswa->id] . ' Alpa Terdeteksi' : null,
                ];
            }
        }

        // Attendance stats for today
        $todayStr = Carbon::today()->toDateString();
        $todayAttendance = AttendanceDaily::whereIn('student_id', $siswaIds)
            ->where('date', $todayStr)
            ->get();

        $totalSiswa = count($siswas);
        $hadirToday = $todayAttendance->whereIn('status', ['H', 'D'])->count();
        $absensiPersen = $totalSiswa > 0 ? round(($hadirToday / $totalSiswa) * 100) : 100;

        // Pending Submissions from Seksi Absensi
        $pendingSubmissions = AttendanceSubmission::with('submittedBy')
            ->where('class_id', $kelas->id)
            ->where('status', 'pending')
            ->orderBy('date', 'desc')
            ->get();

        // Homeroom Journal entries (Pembinaan / Kunjungan Rumah)
        $journals = HomeroomJournal::where('class_id', $kelas->id)
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.wali_kelas.wali_kelas', [
            'kelas' => $kelas,
            'totalSiswa' => $totalSiswa,
            'absensiPersen' => $absensiPersen,
            'priorityStudents' => $priorityStudents,
            'pendingSubmissions' => $pendingSubmissions,
            'journals' => $journals,
            'siswas' => $siswas
        ]);
    }

    /**
     * Profil Siswa 360°.
     */
    public function profile360($id)
    {
        $homeroom = $this->getActiveHomeroom();
        if (!$homeroom) {
            abort(403, 'Akses ditolak.');
        }

        // Eager load everything for this single student to avoid N+1 query problem
        $siswa = Siswa::with([
            'user',
            'kelas',
            'studentGuardians',
            'attendanceDaily' => function($q) {
                $q->orderBy('date', 'desc');
            },
            'academicSnapshots.subject',
            'achievementsViolations' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'studentInterventions.ownerUser',
            'promotionDecisions'
        ])
        ->where('kelas_id', $homeroom->class_id)
        ->findOrFail($id);

        // Fetch BK Referrals but strictly show only basic status info (Privacy check)
        $bkReferrals = BkReferral::where('student_id', $siswa->id)
            ->select('id', 'reason', 'priority', 'status', 'summary_for_homeroom', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate violation points accumulation
        $totalPoinPelanggaran = $siswa->achievementsViolations
            ->where('type', 'pelanggaran')
            ->sum('points');

        $totalPoinPrestasi = $siswa->achievementsViolations
            ->where('type', 'prestasi')
            ->sum('points');

        // History of parent communications
        $communications = ParentCommunication::where('student_id', $siswa->id)
            ->with('guardian')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.wali_kelas.wali_siswa_360', [
            'siswa' => $siswa,
            'bkReferrals' => $bkReferrals,
            'totalPoinPelanggaran' => $totalPoinPelanggaran,
            'totalPoinPrestasi' => $totalPoinPrestasi,
            'communications' => $communications,
        ]);
    }

    /**
     * Halaman Absensi & Verifikasi untuk Wali Kelas.
     */
    public function absensiIndex()
    {
        $homeroom = $this->getActiveHomeroom();
        if (!$homeroom) {
            abort(403, 'Akses ditolak.');
        }

        $kelas = $homeroom->class;

        // Fetch submissions that need verification and history
        $pendingSubmissions = AttendanceSubmission::with(['submittedBy', 'verifications.verifier'])
            ->where('class_id', $kelas->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('dashboard.wali_kelas.wali_absensi_verifikasi', [
            'kelas' => $kelas,
            'submissions' => $pendingSubmissions,
        ]);
    }

    /**
     * Verifikasi Absensi (Terima / Kembalikan / Koreksi).
     */
    public function verifySubmission(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:Terima,Kembalikan,Ajukan Koreksi',
            'note' => 'nullable|string|max:500',
        ]);

        $submission = AttendanceSubmission::findOrFail($id);
        $user = Auth::user();

        // Verify the submission is indeed for the teacher's class
        $homeroom = $this->getActiveHomeroom();
        if (!$homeroom || $submission->class_id !== $homeroom->class_id) {
            abort(403, 'Anda tidak memiliki hak untuk memverifikasi absensi kelas ini.');
        }

        DB::beginTransaction();
        try {
            // Update submission status based on decision
            $statusMap = [
                'Terma' => 'verified', // typo tolerant map
                'Terima' => 'verified',
                'Kembalikan' => 'rejected',
                'Ajukan Koreksi' => 'pending_correction',
            ];

            $newStatus = $statusMap[$request->decision] ?? 'verified';
            $submission->update(['status' => $newStatus]);

            // Save verification decision
            AttendanceVerification::create([
                'submission_id' => $submission->id,
                'verifier_id' => $user->id,
                'decision' => $request->decision,
                'note' => $request->note,
            ]);

            // If verified, update the actual daily attendance verification status
            if ($newStatus === 'verified') {
                AttendanceDaily::where('class_id', $submission->class_id)
                    ->where('date', $submission->date)
                    ->update([
                        'verified_by' => $user->id,
                        'verification_status' => 'verified'
                    ]);
            } elseif ($newStatus === 'rejected') {
                AttendanceDaily::where('class_id', $submission->class_id)
                    ->where('date', $submission->date)
                    ->update([
                        'verification_status' => 'draft' // Return to draft so officer can edit
                    ]);
            }

            // Write to Audit Log
            AttendanceAuditLog::create([
                'entity' => 'attendance_submissions',
                'entity_id' => $submission->id,
                'user_id' => $user->id,
                'old_value' => 'pending',
                'new_value' => $newStatus,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Verifikasi absensi tanggal ' . Carbon::parse($submission->date)->translatedFormat('d F Y') . ' berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
