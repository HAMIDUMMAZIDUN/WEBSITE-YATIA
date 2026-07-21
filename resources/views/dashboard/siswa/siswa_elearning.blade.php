<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            E-learning (Materi & Tugas)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Student Header Profile Banner -->
            @if($siswa)
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
                </div>
            @endif

            <!-- E-learning Materials / Assignments -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-900">E-learning Modul</h3>
                    <span class="px-3 py-1 rounded bg-purple-50 text-purple-600 font-semibold text-xs uppercase tracking-wider">Tersedia</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/50 text-slate-400 font-semibold text-xs uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Mata Pelajaran / Judul</th>
                                <th class="px-6 py-3">Tipe</th>
                                <th class="px-6 py-3">Pengunggah</th>
                                <th class="px-6 py-3 text-right">Batas Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($materiTugas as $mt)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-slate-900">{{ $mt->judul }}</span>
                                        <span class="block text-xs text-slate-400 truncate max-w-xs">{{ $mt->deskripsi ?? 'Tanpa deskripsi' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider {{ $mt->tipe === 'materi' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ ucfirst($mt->tipe) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block font-medium text-slate-700 text-xs">{{ $mt->guru->nama }}</span>
                                        <span class="block text-slate-400 text-[10px]">{{ $mt->mataPelajaran->nama_mapel }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-xs text-slate-500">
                                        {{ $mt->batas_waktu ? Carbon\Carbon::parse($mt->batas_waktu)->format('d M Y, H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada materi atau tugas e-learning.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
