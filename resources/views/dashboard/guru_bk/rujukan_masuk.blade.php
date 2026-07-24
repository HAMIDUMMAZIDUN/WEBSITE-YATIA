<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-bold text-purple-650 dark:text-purple-400 uppercase tracking-widest">Bimbingan Konseling (BK)</span>
            <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                {{ __('Rujukan Masuk & Triase Kasus') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Notification Toast -->
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Incoming Referrals & Decisions (Triage) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Incoming Referrals Queue -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-850 flex justify-between items-center bg-slate-50/50 dark:bg-slate-850/30">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Antrean Rujukan Masuk</h3>
                            <p class="text-xs text-slate-400">Rujukan kasus siswa dari Guru Mapel atau Wali Kelas yang butuh triase penanganan.</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded bg-purple-50 dark:bg-purple-950/20 text-purple-650 dark:text-purple-400 text-xs font-bold uppercase animate-pulse">{{ $referralsQueue->count() }} Rujukan</span>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($referralsQueue as $ref)
                            <div class="p-6 flex flex-col md:flex-row md:items-start justify-between gap-4 hover:bg-slate-50/30 transition-colors">
                                <div class="space-y-2 max-w-xl">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 text-[10px] font-bold uppercase font-mono">Kelas {{ $ref->student->kelas->nama_kelas ?? 'N/A' }}</span>
                                        <span class="text-xs text-slate-400 font-medium">Dirujuk oleh: {{ $ref->createdBy->name ?? 'Sistem' }}</span>
                                    </div>
                                    <h4 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $ref->student->nama }}</h4>
                                    <p class="text-sm text-slate-650 dark:text-slate-300">
                                        <span class="font-bold">Alasan Rujukan:</span> {{ $ref->reason }}
                                    </p>
                                </div>
                                <div class="shrink-0 flex items-center md:self-center">
                                    <button type="button" onclick="openTriagePanel({{ $ref->id }}, '{{ addslashes($ref->student->nama) }}', '{{ addslashes($ref->reason) }}')" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                                        Proses Triase & Buka Kasus
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 font-medium">Tidak ada rujukan baru yang perlu diproses. Antrean bersih!</div>
                        @endforelse
                    </div>
                </div>

                <!-- Active Cases Monitor -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-850">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Kasus Aktif Berjalan</h3>
                        <p class="text-xs text-slate-400">Kasus konseling yang sedang ditangani secara aktif oleh konselor BK.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-3">Siswa / Kelas</th>
                                    <th class="px-6 py-3">Prioritas</th>
                                    <th class="px-6 py-3">Fokus Kasus / Tujuan</th>
                                    <th class="px-6 py-3 text-center">Konselor</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($activeCases as $case)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="block font-bold text-slate-900 dark:text-white">{{ $case->referral->student->nama ?? 'Siswa' }}</span>
                                            <span class="block text-[10px] text-slate-400 font-mono">Kelas {{ $case->referral->student->kelas->nama_kelas ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $case->priority === 'darurat' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/20 dark:text-rose-400' : '' }}
                                                {{ $case->priority === 'tinggi' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/20 dark:text-amber-400' : '' }}
                                                {{ $case->priority === 'sedang' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/20 dark:text-blue-400' : '' }}
                                                {{ $case->priority === 'rendah' ? 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400' : '' }}">
                                                {{ $case->priority }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 max-w-xs truncate">
                                            <span class="font-semibold block text-slate-700 dark:text-slate-350">{{ $case->goal }}</span>
                                            <span class="text-xs text-slate-400 block">{{ $case->plan }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-medium text-slate-800 dark:text-slate-300">
                                            {{ $case->counselor->nama ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('bk.cases.show', $case->id) }}" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/30 dark:hover:bg-purple-900/50 text-purple-650 dark:text-purple-400 rounded-lg text-xs font-bold transition-colors">
                                                Lihat & Tangani
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">Belum ada kasus aktif yang sedang ditangani.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Integrated Triage Process Panel (Single Screen layout) -->
            <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-lg text-purple-650 dark:text-purple-400 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    Panel Form Triase
                </h3>
                <p class="text-xs text-slate-400">Pilih rujukan dari antrean di samping untuk mengisi prioritas dan rencana tindakan konseling.</p>
                
                <div id="triage-empty-state" class="p-8 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-center text-xs text-slate-400 italic">
                    Pilih rujukan untuk memuat panel keputusan triase.
                </div>

                <form id="triage-form" method="POST" action="" class="space-y-4 hidden">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nama Siswa</label>
                        <input type="text" id="triage-student-name" disabled class="w-full bg-slate-50 dark:bg-slate-850 border-0 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-350">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Keluhan Rujukan</label>
                        <textarea id="triage-reason" disabled class="w-full bg-slate-50 dark:bg-slate-850 border-0 rounded-xl px-4 py-2.5 text-xs text-slate-500 resize-none" rows="3"></textarea>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Skala Prioritas Kasus</label>
                        <select name="priority" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 font-semibold">
                            <option value="rendah">Rendah (Penyelesaian Mandiri)</option>
                            <option value="sedang">Sedang (Konseling Terjadwal)</option>
                            <option value="tinggi">Tinggi (Intervensi Cepat)</option>
                            <option value="darurat">Darurat (Tindakan Instan / Krisis)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Target / Sasaran Penanganan</label>
                        <input type="text" name="goal" required placeholder="Contoh: Siswa sadar kehadiran dan komitmen belajar" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Rencana Kegiatan Konseling</label>
                        <textarea name="plan" required placeholder="Contoh: Pemanggilan siswa, konseling individu, dan koordinasi dengan wali kelas." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-purple-500 resize-none" rows="3"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-lg transition-colors">
                        Mulai Kasus Konseling
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTriagePanel(referralId, studentName, reason) {
            // Hide empty state
            document.getElementById('triage-empty-state').classList.add('hidden');
            
            // Show and configure form
            const form = document.getElementById('triage-form');
            form.classList.remove('hidden');
            form.action = '/bk/triase/' + referralId + '/process';
            
            document.getElementById('triage-student-name').value = studentName;
            document.getElementById('triage-reason').value = reason;
        }
    </script>
</x-app-layout>
