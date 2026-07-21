<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                {{ __('Riwayat Kronologis Siswa') }}
            </h2>
            <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Student Header Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-6">
                <div class="h-16 w-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-xl text-slate-700 uppercase">
                    {{ substr($siswa->nama, 0, 2) }}
                </div>
                <div class="flex-1 space-y-1">
                    <h3 class="text-xl font-bold text-slate-900">{{ $siswa->nama }}</h3>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                        <span>NIS: <strong class="text-slate-800">{{ $siswa->nis }}</strong></span>
                        <span>&bull;</span>
                        <span>Kelas: <strong class="text-slate-800">{{ $siswa->kelas->nama_kelas ?? 'Belum Ditentukan' }}</strong></span>
                        <span>&bull;</span>
                        <span>Gender: <strong class="text-slate-800">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}</strong></span>
                        <span>&bull;</span>
                        <span>Poin BK: <strong class="text-rose-600">{{ $siswa->poin_bk }} Poin</strong></span>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-250">
                        {{ $siswa->status }}
                    </span>
                </div>
            </div>

            <!-- Vertical Timeline -->
            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-8">
                    <h3 class="text-base font-bold text-slate-800">Timeline Aktivitas & Akademik</h3>
                    <p class="text-xs text-slate-500">Log kronologis perubahan status, mutasi kelas, absensi, dan bimbingan konseling.</p>
                </div>

                <div class="relative border-l-2 border-slate-150 ml-4 space-y-8">
                    @forelse($timeline as $event)
                        <div class="relative pl-8">
                            <!-- Bullet Icon -->
                            <span class="absolute -left-[17px] top-1.5 flex items-center justify-center w-8 h-8 rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $event['icon'] }}" />
                                </svg>
                            </span>
                            <div class="space-y-1 bg-slate-50/50 hover:bg-slate-50 p-4 rounded-2xl border border-slate-100 transition-colors">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($event['date'])->translatedFormat('d F Y, H:i') }}</span>
                                <h4 class="text-sm font-bold text-slate-800">{{ $event['title'] }}</h4>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $event['description'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-sm">Belum ada catatan aktivitas untuk siswa ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
