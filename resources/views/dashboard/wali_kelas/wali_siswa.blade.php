<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Siswa Aktif Kelas Binaan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- homeroom banner link back -->
            @if($kelas)
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <span class="px-3 py-1 rounded bg-amber-500/10 text-amber-600 font-semibold text-xs uppercase tracking-wider">Kelas Binaan</span>
                        <h3 class="text-3xl font-extrabold text-slate-900">Kelas {{ $kelas->nama_kelas }}</h3>
                        <p class="text-slate-500 text-sm">
                            Berikut adalah daftar administratif seluruh siswa aktif yang berada di bawah bimbingan Anda.
                        </p>
                    </div>
                    <div class="shrink-0 bg-slate-50 px-6 py-4 rounded-2xl border border-slate-100 text-center md:text-left">
                        <span class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Siswa Terdaftar</span>
                        <span class="text-3xl font-black text-slate-900">{{ $siswas->count() }} Siswa</span>
                    </div>
                </div>
            @endif

            <!-- Homeroom Student List Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-900">Daftar Siswa Kelas</h3>
                    <span class="px-3 py-1 rounded bg-slate-100 text-slate-650 font-semibold text-xs uppercase tracking-wider">Daftar Aktif</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Nama Siswa</th>
                                <th class="px-6 py-3">NIS</th>
                                <th class="px-6 py-3 text-center">Poin BK</th>
                                <th class="px-6 py-3 text-right">Rata-Rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswas as $siswa)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $siswa->nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $siswa->nis }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded text-xs font-bold 
                                            {{ $siswa->poin_bk > 50 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}
                                        ">
                                            {{ $siswa->poin_bk }} Poin
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-900 font-mono">
                                        @php
                                            $siswaNilai = $nilais->where('siswa_id', $siswa->id);
                                            $avg = $siswaNilai->count() > 0 ? round($siswaNilai->avg('nilai'), 1) : null;
                                        @endphp
                                        {{ $avg ? $avg : 'Belum dinilai' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">Tidak ada siswa yang terdaftar di kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
