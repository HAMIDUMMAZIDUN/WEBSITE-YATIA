<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-purple-650 dark:text-purple-400 uppercase tracking-widest">Detail Kasus BK</span>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                    Penanganan Kasus: {{ $case->referral->student->nama ?? 'Siswa' }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bk.triase.index') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350 hover:bg-slate-50 rounded-xl font-bold text-xs transition-colors">
                    ← Kembali
                </a>
                @if($case->status === 'terbuka')
                    <form method="POST" action="{{ route('bk.cases.close', $case->id) }}" onsubmit="return confirm('Selesaikan kasus ini? Status akan dikunci dan rujukan ditutup.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
                            ✓ Selesaikan Kasus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-450 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Case Overview Cards -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="text-[10px] font-bold text-slate-450 uppercase block">Sasaran Penanganan</span>
                <p class="text-sm font-semibold text-slate-800 dark:text-white mt-1">{{ $case->goal }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-450 uppercase block">Rencana Konseling</span>
                <p class="text-sm text-slate-650 dark:text-slate-350 mt-1">{{ $case->plan }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-450 uppercase block">Detail Kasus</span>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                        {{ $case->priority === 'darurat' ? 'bg-rose-100 text-rose-850' : '' }}
                        {{ $case->priority === 'tinggi' ? 'bg-amber-100 text-amber-850' : '' }}
                        {{ $case->priority === 'sedang' ? 'bg-blue-100 text-blue-850' : '' }}
                        {{ $case->priority === 'rendah' ? 'bg-slate-100 text-slate-850' : '' }}">
                        {{ $case->priority }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $case->status === 'terbuka' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        Kasus {{ ucfirst($case->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Timeline (Left) -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-6">
                <h3 class="font-bold text-lg text-slate-950 dark:text-white">Kronologi & Timeline Tindakan</h3>
                
                <div class="relative pl-6 border-l border-slate-150 dark:border-slate-800 space-y-6">
                    @foreach($case->timeline as $tl)
                        <div class="relative">
                            <!-- Bullet Indicator -->
                            <span class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full border-4 border-white dark:border-slate-900
                                {{ $tl->event_type === 'rujukan' ? 'bg-blue-500' : '' }}
                                {{ $tl->event_type === 'sesi' ? 'bg-purple-500' : '' }}
                                {{ $tl->event_type === 'penutupan' ? 'bg-emerald-500' : '' }}">
                            </span>
                            
                            <div class="space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white uppercase">
                                        {{ $tl->event_type }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400">
                                        {{ Carbon\Carbon::parse($tl->event_date)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-650 dark:text-slate-300">{{ $tl->note }}</p>
                                <span class="block text-[10px] text-slate-400">Diproses oleh: {{ $tl->actor->name ?? 'Staf' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sessions & Record form (Right) -->
            <div class="lg:col-span-5 space-y-8">
                <!-- Add counseling session (Only active) -->
                @if($case->status === 'terbuka')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="font-bold text-lg text-purple-650 dark:text-purple-400 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                            Catat Sesi Konseling Baru
                        </h3>
                        
                        <form action="{{ route('bk.sessions.store', $case->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Sesi</label>
                                    <input type="date" name="date" required value="{{ Carbon\Carbon::today()->toDateString() }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 font-semibold">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Durasi (Menit)</label>
                                    <input type="number" name="duration" required value="45" min="5" max="300" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 font-semibold">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Layanan Sesi</label>
                                    <select name="service_type" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 font-semibold">
                                        <option value="Konseling Individu">Individual Counseling</option>
                                        <option value="Konseling Kelompok">Group Counseling</option>
                                        <option value="Mediasi Konflik">Conflict Mediation</option>
                                        <option value="Kunjungan Rumah">Home Visit</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tingkat Kerahasiaan</label>
                                    <select name="privacy_level" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 font-semibold">
                                        <option value="internal_school">Internal Sekolah (Guru/Wali)</option>
                                        <option value="confidential">Rahasia (BK/Wali)</option>
                                        <option value="strict_confidential">Sangat Rahasia (Hanya BK)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tujuan Sesi</label>
                                <input type="text" name="objective" required placeholder="Contoh: Menggali akar penyebab keterlambatan" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan Detail Konseling (Sensitif)</label>
                                <textarea name="summary" required placeholder="Catatan intim proses konseling. Data akan disimpan secara terenkripsi di server." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 resize-none" rows="4"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rencana Tindak Lanjut</label>
                                <input type="text" name="follow_up" placeholder="Contoh: Pemantauan kehadiran 1 minggu ke depan" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unggah Berkas Pendukung (Dokumen Rahasia)</label>
                                <input type="file" name="attachment" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 hover:file:bg-slate-200">
                            </div>

                            <button type="submit" class="w-full py-3 bg-purple-650 hover:bg-purple-600 text-white font-bold rounded-xl shadow-lg transition-colors">
                                Simpan Sesi (Terenkripsi)
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Sessions List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-950 dark:text-white">Daftar Sesi Konseling</h3>
                    <div class="space-y-3">
                        @forelse($case->sessions as $session)
                            <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-850/50 flex justify-between items-center gap-3">
                                <div>
                                    <span class="px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 font-bold text-[9px] uppercase tracking-wider">{{ $session->service_type }}</span>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-1">{{ $session->objective }}</h4>
                                    <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-mono">
                                        <span>{{ Carbon\Carbon::parse($session->date)->translatedFormat('d M Y') }}</span>
                                        <span>•</span>
                                        <span>{{ $session->duration }} mnt</span>
                                    </div>
                                </div>
                                
                                <button type="button" onclick="viewSessionDetails({{ $session->id }})" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-[10px] rounded-lg shadow-sm transition-colors">
                                    Detail
                                </button>
                            </div>
                        @empty
                            <p class="text-xs text-slate-450 italic text-center py-4">Belum ada sesi konseling yang dilaksanakan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Decrypted Details Modal -->
    <div id="modal-session-details" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-850 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                <div>
                    <h4 class="font-bold text-slate-950 dark:text-white">Detail Catatan Konseling Terenkripsi</h4>
                    <span class="text-xs text-slate-400" id="session-modal-title">Sesi</span>
                </div>
                <button type="button" onclick="closeModal('modal-session-details')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-450 block font-bold uppercase text-[9px]">Siswa</span>
                        <span id="session-detail-student" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div>
                        <span class="text-slate-450 block font-bold uppercase text-[9px]">Kerahasiaan</span>
                        <span id="session-detail-privacy" class="font-semibold text-purple-650"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-450 block font-bold uppercase text-[9px]">Tanggal</span>
                        <span id="session-detail-date" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div>
                        <span class="text-slate-450 block font-bold uppercase text-[9px]">Durasi</span>
                        <span id="session-detail-duration" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-850 pt-4">
                    <span class="text-slate-450 block font-bold uppercase text-[9px] mb-1">Tujuan Sesi</span>
                    <p id="session-detail-objective" class="text-xs text-slate-700 dark:text-slate-350 font-semibold"></p>
                </div>

                <div class="bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/50 p-4 rounded-xl">
                    <span class="text-purple-650 dark:text-purple-400 block font-bold uppercase text-[9px] mb-1">Isi Konseling (Decrypted)</span>
                    <p id="session-detail-summary" class="text-xs text-slate-850 dark:text-slate-200 whitespace-pre-line leading-relaxed"></p>
                </div>

                <div>
                    <span class="text-slate-450 block font-bold uppercase text-[9px] mb-1">Rencana Tindak Lanjut</span>
                    <p id="session-detail-followup" class="text-xs text-slate-750 dark:text-slate-300"></p>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-850 flex justify-end">
                <button type="button" onclick="closeModal('modal-session-details')" class="px-5 py-2.5 bg-slate-850 text-white rounded-xl text-xs font-bold transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function viewSessionDetails(sessionId) {
            // Fetch session details via AJAX to perform field-level decryption on demand
            fetch('/bk/sessions/' + sessionId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('session-modal-title').innerText = data.service_type;
                    document.getElementById('session-detail-student').innerText = data.student_name;
                    document.getElementById('session-detail-privacy').innerText = data.privacy_level;
                    document.getElementById('session-detail-date').innerText = data.date;
                    document.getElementById('session-detail-duration').innerText = data.duration;
                    document.getElementById('session-detail-objective').innerText = data.objective;
                    document.getElementById('session-detail-summary').innerText = data.summary;
                    document.getElementById('session-detail-followup').innerText = data.follow_up;
                    
                    openModal('modal-session-details');
                })
                .catch(error => {
                    alert('Gagal mendownload data sesi. Silakan coba kembali.');
                });
        }
    </script>
</x-app-layout>
