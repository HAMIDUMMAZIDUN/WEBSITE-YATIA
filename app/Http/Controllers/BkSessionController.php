<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BkCase;
use App\Models\BkSession;
use App\Models\BkCaseTimeline;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BkSessionController extends Controller
{
    public function __construct()
    {
        // Enforce role-based access checks
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->isGuruBk()) {
                AuditLog::create([
                    'user_id' => $user ? $user->id : null,
                    'action' => 'unauthorized_session_attempt',
                    'entity' => 'bk_sessions',
                    'entity_id' => null,
                ]);
                abort(403, 'Akses Ditolak. Sesi konseling hanya dapat diakses oleh Guru BK.');
            }
            return $next($request);
        });
    }

    /**
     * Store a counseling session.
     */
    public function store(Request $request, $caseId)
    {
        $request->validate([
            'service_type' => 'required|string|max:255',
            'date' => 'required|date',
            'duration' => 'required|integer|min:5|max:300',
            'objective' => 'required|string|max:1000',
            'summary' => 'required|string', // Very sensitive text
            'follow_up' => 'nullable|string|max:1000',
            'privacy_level' => 'required|in:public,internal_school,confidential,strict_confidential',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,docx|max:5120', // Max 5MB
        ]);

        $case = BkCase::findOrFail($caseId);
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Encrypt summary for field-level privacy
            $encryptedSummary = Crypt::encryptString($request->summary);

            // Handle confidential file upload (stored in secure disk, not public)
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Store in secure non-public storage
                $path = $file->store('bk_attachments', 'local'); // 'local' is not accessible via public URL
                $attachmentPath = $path;
            }

            // Create Session
            $session = BkSession::create([
                'case_id' => $case->id,
                'service_type' => $request->service_type,
                'date' => $request->date,
                'duration' => $request->duration,
                'objective' => $request->objective,
                'summary' => $encryptedSummary, // saved encrypted
                'follow_up' => $request->follow_up,
                'privacy_level' => $request->privacy_level,
                'attachment' => $attachmentPath, // path reference
            ]);

            // Add timeline entry
            BkCaseTimeline::create([
                'case_id' => $case->id,
                'event_type' => 'sesi',
                'event_date' => $request->date,
                'note' => 'Melaksanakan Sesi Konseling: ' . $request->service_type,
                'actor_id' => $user->id,
                'visibility' => ($request->privacy_level === 'public') ? 'shared_homeroom' : 'bk_only',
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'create_session',
                'entity' => 'bk_sessions',
                'entity_id' => $session->id,
            ]);

            DB::commit();
            return redirect()->route('bk.cases.show', $case->id)->with('success', 'Sesi konseling berhasil dicatat secara aman.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan sesi: ' . $e->getMessage());
        }
    }

    /**
     * Show a session (decrypted).
     */
    public function show($id)
    {
        $session = BkSession::with('case.referral.student')->findOrFail($id);
        $user = Auth::user();

        // Audit read session action
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'read_session_details',
            'entity' => 'bk_sessions',
            'entity_id' => $session->id,
        ]);

        // Decrypt summary for reading
        try {
            $decryptedSummary = Crypt::decryptString($session->summary);
        } catch (\Exception $e) {
            $decryptedSummary = '[Gagal Mendekripsi Catatan. Kunci enkripsi rusak atau tidak cocok.]';
        }

        return response()->json([
            'service_type' => $session->service_type,
            'date' => Carbon::parse($session->date)->translatedFormat('d F Y'),
            'duration' => $session->duration . ' Menit',
            'objective' => $session->objective,
            'summary' => $decryptedSummary,
            'follow_up' => $session->follow_up ?? '-',
            'privacy_level' => strtoupper($session->privacy_level),
            'has_attachment' => !empty($session->attachment),
            'student_name' => $session->case->referral->student->nama ?? 'Siswa',
        ]);
    }
}
