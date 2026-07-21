<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Verifikasi & Persetujuan Absensi</span>
            <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-100 leading-tight">
                {{ __('Verifikasi Laporan Kehadiran Kelas') }} {{ $kelas->nama_kelas }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
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

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar Kiriman Absensi</h3>
                    <p class="text-xs text-slate-400">Tinjau, setujui, atau tolak draf absensi yang dikirimkan oleh petugas absensi kelas.</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold text-xs uppercase border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Tanggal Laporan</th>
                            <th class="px-6 py-4">Pengirim</th>
                            <th class="px-6 py-4">Status Laporan</th>
                            <th class="px-6 py-4">Waktu Kirim</th>
                            <th class="px-6 py-4">Catatan Verifikasi</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($submissions as $sub)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                    {{ Carbon\Carbon::parse($sub->date)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300">
                                    {{ $sub->submittedBy->name ?? 'Petugas Absensi' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($sub->status === 'draft')
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-450">Draf</span>
                                    @elseif($sub->status === 'pending')
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-amber-100 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 animate-pulse">Menunggu Verifikasi</span>
                                    @elseif($sub->status === 'verified')
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-emerald-100 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400">Terverifikasi</span>
                                    @elseif($sub->status === 'pending_correction')
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-rose-100 dark:bg-rose-950/20 text-rose-700 dark:text-rose-450">Butuh Koreksi</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500">Dikembalikan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-450">
                                    {{ $sub->submitted_at ? Carbon\Carbon::parse($sub->submitted_at)->translatedFormat('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    @if($sub->verifications->isNotEmpty())
                                        <div class="space-y-1">
                                            <span class="block font-semibold text-slate-700 dark:text-slate-350">Oleh: {{ $sub->verifications->last()->verifier->name ?? 'Wali Kelas' }}</span>
                                            <p class="italic">"{{ $sub->verifications->last()->note ?? 'Tanpa catatan' }}"</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Belum ditinjau</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Review action only available for pending verification -->
                                        @if($sub->status === 'pending')
                                            <button type="button" onclick="openReviewModal({{ $sub->id }}, '{{ Carbon\Carbon::parse($sub->date)->translatedFormat('d F Y') }}')" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                                                Tinjau & Verifikasi
                                            </button>
                                        @else
                                            <button disabled class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-650 rounded-xl text-xs font-semibold">
                                                Selesai
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada kiriman absensi untuk kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Review & Verification Modal -->
    <div id="modal-review" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-850 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
                <div>
                    <h4 class="font-bold text-slate-950 dark:text-white">Verifikasi Absensi Kelas</h4>
                    <span class="text-xs text-slate-400" id="review-title-date">Tanggal</span>
                </div>
                <button type="button" onclick="closeModal('modal-review')" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
            </div>
            
            <form id="form-review-submit" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Keputusan Verifikasi</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="decision" value="Terima" checked class="sr-only peer">
                            <div class="py-3 text-center border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-emerald-600 dark:text-emerald-450 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-transparent transition-all">
                                Setujui (Sesuai)
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="decision" value="Kembalikan" class="sr-only peer">
                            <div class="py-3 text-center border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-amber-600 dark:text-amber-450 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-transparent transition-all">
                                Kembalikan draf
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="decision" value="Ajukan Koreksi" class="sr-only peer">
                            <div class="py-3 text-center border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-450 peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-transparent transition-all">
                                Butuh Koreksi
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan / Umpan Balik</label>
                    <textarea name="note" required placeholder="Contoh: Absensi disetujui, bukti sakit Ahmad sudah sesuai. Atau: Silakan ganti status Budi ke Alpa." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 resize-none" rows="4"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-review')" class="flex-1 py-3 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350 hover:bg-slate-50 rounded-xl text-xs font-bold transition-colors">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-lg transition-colors">Kirim Keputusan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function openReviewModal(submissionId, dateString) {
            document.getElementById('review-title-date').innerText = 'Laporan Kehadiran Tanggal ' + dateString;
            document.getElementById('form-review-submit').action = '/wali/absensi/verifikasi/' + submissionId;
            openModal('modal-review');
        }
    </script>
</x-app-layout>
