<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BkReferral;
use App\Models\BkCase;
use App\Models\BkCaseTimeline;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BkCaseController extends Controller
{
    private function checkGuruBk()
    {
        $user = Auth::user();
        if (!$user || !$user->isGuruBk()) {
            // Log unauthorized access attempt
            AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => 'unauthorized_access_attempt',
                'entity' => 'bk_cases',
                'entity_id' => null,
                'old_value' => null,
                'new_value' => 'Attempted to access BK Case Management',
            ]);
            abort(403, 'Akses Ditolak. Halaman ini hanya boleh diakses oleh Guru Bimbingan Konseling (BK).');
        }
    }

    /**
     * Display the Referral Incoming and Triase Panel on a single screen.
     */
    public function triaseIndex()
    {
        $this->checkGuruBk();
        $user = Auth::user();

        // Get referrals that are still pending
        $referralsQueue = BkReferral::with(['student.kelas', 'createdBy'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // Get cases currently open
        $activeCases = BkCase::with(['referral.student.kelas', 'counselor'])
            ->where('status', 'terbuka')
            ->orderBy('opened_at', 'desc')
            ->get();

        return view('dashboard.guru_bk.rujukan_masuk', [
            'referralsQueue' => $referralsQueue,
            'activeCases' => $activeCases,
        ]);
    }

    /**
     * Store decision for triage (converting referral to an active BK Case).
     */
    public function processTriase(Request $request, $id)
    {
        $this->checkGuruBk();
        $request->validate([
            'priority' => 'required|in:rendah,sedang,tinggi,darurat',
            'goal' => 'required|string|max:1000',
            'plan' => 'required|string|max:1000',
        ]);

        $referral = BkReferral::findOrFail($id);
        $user = Auth::user();
        $guru = $user->guru; // Get associated guru details

        if (!$guru) {
            return redirect()->back()->with('error', 'Detail Guru BK Anda tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // Create Case
            $case = BkCase::create([
                'referral_id' => $referral->id,
                'counselor_id' => $guru->id,
                'goal' => $request->goal,
                'plan' => $request->plan,
                'priority' => $request->priority,
                'status' => 'terbuka',
                'opened_at' => Carbon::now(),
            ]);

            // Update Referral status
            $referral->update([
                'status' => 'diproses',
                'priority' => $request->priority
            ]);

            // Add to timeline
            BkCaseTimeline::create([
                'case_id' => $case->id,
                'event_type' => 'rujukan',
                'event_date' => Carbon::today(),
                'note' => 'Kasus dibuka berdasarkan rujukan dari ' . ($referral->createdBy->name ?? 'Sistem'),
                'actor_id' => $user->id,
                'visibility' => 'shared_homeroom',
            ]);

            // Audit logging
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'open_case',
                'entity' => 'bk_cases',
                'entity_id' => $case->id,
                'new_value' => json_encode(['referral_id' => $referral->id, 'priority' => $request->priority]),
            ]);

            DB::commit();
            return redirect()->route('bk.triase.index')->with('success', 'Rujukan berhasil ditriase menjadi Kasus Aktif.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses triase: ' . $e->getMessage());
        }
    }

    /**
     * Show case timeline and sessions.
     */
    public function show($id)
    {
        $this->checkGuruBk();
        $case = BkCase::with([
            'referral.student.kelas',
            'counselor.user',
            'sessions' => function($q) { $q->orderBy('date', 'desc'); },
            'timeline' => function($q) { $q->orderBy('event_date', 'asc'); }
        ])->findOrFail($id);

        $user = Auth::user();

        // Audit view case action
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'view_case',
            'entity' => 'bk_cases',
            'entity_id' => $case->id,
        ]);

        return view('dashboard.guru_bk.data_siswa_kasus', [
            'case' => $case,
        ]);
    }

    /**
     * Close a case.
     */
    public function closeCase(Request $request, $id)
    {
        $this->checkGuruBk();
        $case = BkCase::findOrFail($id);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $case->update([
                'status' => 'selesai',
                'closed_at' => Carbon::now(),
            ]);

            if ($case->referral) {
                $case->referral->update(['status' => 'selesai']);
            }

            BkCaseTimeline::create([
                'case_id' => $case->id,
                'event_type' => 'penutupan',
                'event_date' => Carbon::today(),
                'note' => 'Kasus diselesaikan dan ditutup.',
                'actor_id' => $user->id,
                'visibility' => 'shared_homeroom',
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'close_case',
                'entity' => 'bk_cases',
                'entity_id' => $case->id,
            ]);

            DB::commit();
            return redirect()->route('bk.triase.index')->with('success', 'Kasus BK berhasil diselesaikan dan ditutup.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menutup kasus: ' . $e->getMessage());
        }
    }
}
