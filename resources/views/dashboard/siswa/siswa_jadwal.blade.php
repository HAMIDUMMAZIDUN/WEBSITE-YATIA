<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-extrabold text-2xl text-slate-900 leading-tight">
            Jadwal Belajar Saya
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

            <!-- Schedule List -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-55 bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-900">Jadwal Kelas</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($jadwals as $jadwal)
                        <div class="px-6 py-4 hover:bg-slate-50/40 transition-colors flex justify-between items-center text-sm">
                            <div class="space-y-0.5">
                                <span class="block font-semibold text-slate-900">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                                <span class="block text-xs text-slate-400">{{ $jadwal->guru->nama }}</span>
                            </div>
                            <div class="text-right space-y-0.5">
                                <span class="block font-bold text-emerald-600 text-xs font-mono uppercase tracking-wider">{{ $jadwal->hari }}</span>
                                <span class="block text-xs text-slate-500 font-mono">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada jadwal belajar yang terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
