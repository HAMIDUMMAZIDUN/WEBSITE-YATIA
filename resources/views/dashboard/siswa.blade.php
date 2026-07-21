<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Dashboard Siswa') }}
            </h2>
            <button onclick="document.getElementById('erapor-student-modal').classList.remove('hidden')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-600/10 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat e-Rapor Saya
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Student Header Profile -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 text-white shadow-lg shadow-purple-600/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded bg-white/20 text-white font-semibold text-xs uppercase tracking-wider">Profil Siswa</span>
                    <h3 class="text-3xl font-extrabold">{{ $siswa->nama }}</h3>
                    <p class="text-purple-100 flex items-center gap-2">
                        <span class="font-mono text-sm bg-black/15 px-2 py-0.5 rounded">NIS: {{ $siswa->nis ?? '-' }}</span>
                        <span class="text-purple-200">|</span>
                        <span>Kelas: {{ $kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-6 shrink-0 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                    <div class="text-center">
                        <span class="block text-purple-200 text-xs font-semibold uppercase tracking-wider">Poin Kedisiplinan BK</span>
                        <span class="text-2xl font-bold {{ $siswa->poin_bk > 50 ? 'text-rose-300' : 'text-emerald-300' }}">
                            {{ $siswa->poin_bk }} Poin
                        </span>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Student e-Rapor Modal -->
    <div id="erapor-student-modal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100">
            <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-bold">e-Rapor Hasil Belajar Kurikulum Merdeka</h4>
                    <p class="text-xs text-slate-400">Pratinjau Nilai Hasil Belajar Anda</p>
                </div>
                <button onclick="document.getElementById('erapor-student-modal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Rapor Details -->
                <div class="grid grid-cols-2 gap-4 border-b border-slate-100 pb-6 text-sm text-slate-600">
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Nama Siswa</span>
                        <span class="font-bold text-slate-900">{{ $siswa->nama }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Kelas</span>
                        <span class="font-bold text-slate-900">{{ $kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                    </div>
                </div>

                <!-- Grades list table -->
                <div class="space-y-3">
                    <h5 class="font-bold text-slate-900 text-sm">Nilai Mata Pelajaran</h5>
                    <div class="border border-slate-100 rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-400 font-semibold text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-2.5">Mata Pelajaran</th>
                                    <th class="px-6 py-2.5 text-center">Tipe Nilai</th>
                                    <th class="px-6 py-2.5 text-right">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($nilais as $nl)
                                    <tr>
                                        <td class="px-6 py-3">
                                            <span class="block font-semibold text-slate-900">{{ $nl->mataPelajaran->nama_mapel }}</span>
                                            <span class="block text-[10px] text-slate-400">Guru: {{ $nl->guru->nama }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-center uppercase text-xs font-medium text-slate-500">{{ $nl->tipe_nilai }}</td>
                                        <td class="px-6 py-3 text-right font-bold text-emerald-600 font-mono">{{ $nl->nilai }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-6 text-center text-slate-400">Belum ada nilai akademik yang dirilis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button onclick="document.getElementById('erapor-student-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    Tutup
                </button>
                <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold shadow transition-colors">
                    Cetak Rapor Saya
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
