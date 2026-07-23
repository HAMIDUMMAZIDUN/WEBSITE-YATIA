<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AttendanceDaily;
use App\Models\AttendanceSubmission;
use App\Models\AttendanceEvidence;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceOfficerAssignment;
use App\Models\AttendanceAuditLog;
use App\Models\AcademicYear;
use App\Models\AcademicHoliday;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceDailyController extends Controller
{
    /**
     * Enforce assignment check for student officers.
     */
    protected function checkAssignment($classId)
    {
        $user = Auth::user();

        // Super Admin or Wali Kelas can view/manage, but student officers are restricted
        if ($user->isSuperAdmin() || $user->isWaliKelas()) {
            return true;
        }

        $activeYear = AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;

        $assigned = AttendanceOfficerAssignment::where('user_id', $user->id)
            ->where('class_id', $classId)
            ->where('active', true)
            ->when($yearId, function($q) use ($yearId) {
                return $q->where('academic_year_id', $yearId);
            })
            ->exists();

        if (!$assigned) {
            abort(403, 'Akses Ditolak. Anda tidak ditugaskan untuk mengelola absensi kelas ini.');
        }

        return true;
    }

    /**
     * Mobile-first attendance input interface.
     */
    public function inputIndex(Request $request, $classId = null)
    {
        $user = Auth::user();
        $activeYear = AcademicYear::where('active', true)->first();
        $yearId = $activeYear ? $activeYear->id : null;

        // If no class is specified, find the first class assigned to this officer or homeroom
        if (!$classId) {
            if ($user->isWaliKelas()) {
                $guru = $user->guru;
                if ($guru) {
                    $homeroom = \App\Models\ClassHomeroom::where('teacher_id', $guru->id)
                        ->where('active', true)
                        ->when($yearId, function($q) use ($yearId) {
                            return $q->where('academic_year_id', $yearId);
                        })
                        ->first();
                    if ($homeroom) {
                        $classId = $homeroom->class_id;
                    }
                }
            }

            if (!$classId) {
                $assignment = AttendanceOfficerAssignment::where('user_id', $user->id)
                    ->where('active', true)
                    ->when($yearId, function($q) use ($yearId) {
                        return $q->where('academic_year_id', $yearId);
                    })
                    ->first();

                if (!$assignment) {
                    return view('dashboard.absensi_empty', [
                        'message' => 'Anda tidak ditugaskan untuk menginput absensi di kelas manapun.'
                    ]);
                }
                $classId = $assignment->class_id;
            }
        }

        $this->checkAssignment($classId);

        $kelas = Kelas::findOrFail($classId);
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get all students in the class
        $siswas = Siswa::where('kelas_id', $classId)->orderBy('nama', 'asc')->get();
        $siswaIds = $siswas->pluck('id');

        // Fetch existing attendance records for this date
        $existingRecords = AttendanceDaily::whereIn('student_id', $siswaIds)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Fetch today's submission status
        $submission = AttendanceSubmission::where('class_id', $classId)
            ->where('date', $date)
            ->first();

        $statusLocks = ['pending', 'verified', 'pending_correction'];
        $isLocked = $submission && in_array($submission->status, $statusLocks);
        $holidayMessage = null;

        // Check against active academic year bounds, school days, and holidays
        if ($activeYear) {
            $classUnit = preg_match('/^(X|XI|XII)/i', $kelas->nama_kelas) ? 'MA' : 'MTs';

            // 1. Range Validation
            if ($activeYear->tanggal_mulai && $activeYear->tanggal_selesai) {
                if ($date < $activeYear->tanggal_mulai || $date > $activeYear->tanggal_selesai) {
                    $isLocked = true;
                    $holidayMessage = "Di luar rentang kalender akademik aktif (" . Carbon::parse($activeYear->tanggal_mulai)->format('d-m-Y') . " s/d " . Carbon::parse($activeYear->tanggal_selesai)->format('d-m-Y') . ").";
                }
            }

            // 2. School Days Validation
            if (!$isLocked && $activeYear->hari_sekolah) {
                $dayName = Carbon::parse($date)->format('l'); // Monday, Tuesday, etc.
                $schoolDays = explode(',', $activeYear->hari_sekolah);
                if (!in_array($dayName, $schoolDays)) {
                    $isLocked = true;
                    $holidayMessage = "Bukan Hari Sekolah. Pengisian absensi dinonaktifkan untuk hari " . $dayName . ".";
                }
            }

            // 3. Holiday Calendar Validation
            if (!$isLocked) {
                $holiday = AcademicHoliday::where('academic_year_id', $activeYear->id)
                    ->where('tanggal_mulai', '<=', $date)
                    ->where('tanggal_selesai', '>=', $date)
                    ->where(function($q) use ($classUnit) {
                        $q->where('unit', 'Semua')
                          ->orWhere('unit', $classUnit);
                    })
                    ->first();

                if ($holiday) {
                    $isLocked = true;
                    $holidayMessage = "Hari Libur: " . $holiday->keterangan . " (" . ($holiday->is_nasional ? 'Libur Nasional' : 'Libur Lokal') . ").";
                }
            }
        }

        return view('dashboard.absensi.absensi_input', [
            'kelas' => $kelas,
            'date' => $date,
            'siswas' => $siswas,
            'existingRecords' => $existingRecords,
            'submission' => $submission,
            'isLocked' => $isLocked,
            'holidayMessage' => $holidayMessage,
        ]);
    }

    /**
     * Bulk save attendance as draft.
     */
    public function saveDraft(Request $request)
    {
        $classId = $request->input('class_id');
        $this->checkAssignment($classId);

        $date = $request->input('date', Carbon::today()->toDateString());
        $attendanceData = $request->input('attendance', []); // Array keyed by student_id => status

        // Check if submission is locked
        $submission = AttendanceSubmission::where('class_id', $classId)
            ->where('date', $date)
            ->first();

        if ($submission && in_array($submission->status, ['pending', 'verified', 'pending_correction'])) {
            return redirect()->back()->with('error', 'Data absensi hari ini sudah dikunci karena sedang dikirim atau diverifikasi.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $upsertData = [];

            foreach ($attendanceData as $studentId => $status) {
                // Bulk prepare
                $record = AttendanceDaily::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                        'input_by' => $user->id,
                        'verification_status' => 'draft',
                    ]
                );

                // Handle evidence files if uploaded (for S/I/D status)
                if (in_array($status, ['S', 'I', 'D']) && $request->hasFile("evidence.{$studentId}")) {
                    $file = $request->file("evidence.{$studentId}");
                    $path = $file->store('attendance_evidence', 'public');

                    AttendanceEvidence::create([
                        'attendance_id' => $record->id,
                        'type' => strtolower($status),
                        'file' => $path,
                        'source' => 'officer',
                    ]);
                }
            }

            // Create or update Draft Submission record
            AttendanceSubmission::updateOrCreate(
                [
                    'class_id' => $classId,
                    'date' => $date,
                ],
                [
                    'submitted_by' => $user->id,
                    'status' => 'draft',
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Draf kehadiran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan draf: ' . $e->getMessage());
        }
    }

    /**
     * Submit attendance to Wali Kelas (locking the draft).
     */
    public function submitToWali(Request $request)
    {
        $classId = $request->input('class_id');
        $this->checkAssignment($classId);

        $date = $request->input('date', Carbon::today()->toDateString());
        $user = Auth::user();

        // Count class students
        $totalSiswa = Siswa::where('kelas_id', $classId)->count();
        $recordedCount = AttendanceDaily::where('class_id', $classId)
            ->where('date', $date)
            ->count();

        if ($recordedCount < $totalSiswa) {
            return redirect()->back()->with('error', 'Gagal mengirim! Pastikan semua siswa (' . $totalSiswa . ' anak) sudah diisi status kehadirannya.');
        }

        DB::beginTransaction();
        try {
            // Update submission status to pending
            $submission = AttendanceSubmission::updateOrCreate(
                [
                    'class_id' => $classId,
                    'date' => $date,
                ],
                [
                    'submitted_by' => $user->id,
                    'status' => 'pending',
                    'submitted_at' => Carbon::now(),
                ]
            );

            // Update all daily records to pending verification
            AttendanceDaily::where('class_id', $classId)
                ->where('date', $date)
                ->update(['verification_status' => 'pending']);

            // Save audit log
            AttendanceAuditLog::create([
                'entity' => 'attendance_submissions',
                'entity_id' => $submission->id,
                'user_id' => $user->id,
                'old_value' => 'draft',
                'new_value' => 'pending',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Laporan absensi berhasil dikirim ke Wali Kelas dan data dikunci.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    /**
     * Submit Correction Request (Official correction pathway).
     */
    public function submitCorrection(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendance_daily,id',
            'new_status' => 'required|in:H,S,I,A,T,D',
            'reason' => 'required|string|max:500',
            'evidence' => 'nullable|file|mimes:pdf,jpg,png,docx|max:2048',
        ]);

        $attendance = AttendanceDaily::findOrFail($request->attendance_id);
        $this->checkAssignment($attendance->class_id);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $path = null;
            if ($request->hasFile('evidence')) {
                $path = $request->file('evidence')->store('correction_evidence', 'public');
            }

            // Create Correction Request
            $correction = AttendanceCorrection::create([
                'attendance_id' => $attendance->id,
                'old_status' => $attendance->status,
                'new_status' => $request->new_status,
                'reason' => $request->reason,
                'evidence' => $path,
                'decision' => 'pending',
            ]);

            // Track audit logs
            AttendanceAuditLog::create([
                'entity' => 'attendance_corrections',
                'entity_id' => $correction->id,
                'user_id' => $user->id,
                'old_value' => $attendance->status,
                'new_value' => $request->new_status,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan koreksi data berhasil diajukan dan menunggu keputusan Wali Kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengajukan koreksi: ' . $e->getMessage());
        }
    }
}
