<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Wali Kelas — {{ $kelas->nama_kelas }}</span>
                <h2 class="font-bold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    Manajemen Petugas Absensi
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('wali.absensi.verifikasi.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Verifikasi Absensi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Toast --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Info Banner --}}
            <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-amber-800 dark:text-amber-300 text-sm">Tentang Petugas Absensi</p>
                    <p class="text-amber-700 dark:text-amber-400 text-xs mt-1 leading-relaxed">
                        Siswa yang Anda tugaskan akan mendapat akses menu <strong>Input Absensi</strong> khusus untuk kelas <strong>{{ $kelas->nama_kelas }}</strong>. 
                        Mereka hanya bisa mengisi absensi kelas ini, bukan kelas lain.
                        Tahun ajaran aktif: <strong>{{ $activeYear ? $activeYear->nama_tahun_ajaran : 'Belum ada' }}</strong>.
                    </p>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Daftar Siswa + Form Penugasan --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">Daftar Siswa Kelas {{ $kelas->nama_kelas }}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pilih siswa untuk ditugaskan sebagai petugas absensi</p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-bold">
                            {{ $siswas->count() }} Siswa
                        </span>
                    </div>

                    <div class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @forelse($siswas as $siswa)
                            @php
                                $assignedEntry = $assignments->firstWhere('user_id', optional($siswa->user)->id);
                                $isAssigned = $assignedEntry && $assignedEntry->active;
                                $isPending  = $assignedEntry && !$assignedEntry->active;
                                $hasUser    = (bool)$siswa->user;
                            @endphp
                            <div class="px-5 py-4 flex items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Avatar --}}
                                    @if($siswa->user?->photo_profile)
                                        <img src="{{ asset('storage/' . $siswa->user->photo_profile) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 text-white font-bold text-sm flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <span class="block font-semibold text-sm text-slate-900 dark:text-white truncate">{{ $siswa->nama }}</span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-mono text-slate-400">{{ $siswa->nis }}</span>
                                            @if(!$hasUser)
                                                <span class="text-[9px] px-1.5 py-0.5 bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded font-bold">Tanpa Akun</span>
                                            @elseif($isAssigned)
                                                <span class="text-[9px] px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded font-bold">Petugas Aktif</span>
                                            @elseif($isPending)
                                                <span class="text-[9px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded font-bold">Dinonaktifkan</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 shrink-0">
                                    @if($hasUser)
                                        <form action="{{ route('wali.officer.assign') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $siswa->user->id }}">
                                            @if($isAssigned)
                                                <button type="submit" onclick="return confirm('Nonaktifkan penugasan {{ $siswa->nama }} sebagai petugas absensi?')"
                                                    class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg transition-colors">
                                                    Nonaktifkan
                                                </button>
                                            @elseif($isPending)
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-lg transition-colors">
                                                    Aktifkan Kembali
                                                </button>
                                                <form action="{{ route('wali.officer.revoke', $assignedEntry->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Hapus permanen penugasan ini?')"
                                                        class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-lg transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-lg shadow-sm transition-colors">
                                                    Tugaskan
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 text-slate-400 text-xs font-medium rounded-lg cursor-not-allowed">
                                            Tidak bisa ditugaskan
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium">Belum ada siswa di kelas ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Petugas Aktif --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base">Petugas Absensi Aktif</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Siswa yang sudah ditugaskan & aktif</p>
                            </div>
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-bold">
                                {{ $assignments->where('active', true)->count() }} Aktif
                            </span>
                        </div>

                        @php $activeOfficers = $assignments->where('active', true); @endphp

                        @if($activeOfficers->isEmpty())
                            <div class="px-6 py-10 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm font-medium">Belum ada petugas absensi aktif</p>
                                <p class="text-xs mt-1">Tugaskan siswa dari daftar sebelah kiri</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                @foreach($activeOfficers as $assignment)
                                    <div class="px-5 py-4 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            @if($assignment->user?->photo_profile)
                                                <img src="{{ asset('storage/' . $assignment->user->photo_profile) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-white font-bold text-sm flex items-center justify-center">
                                                    {{ strtoupper(substr($assignment->user?->name ?? '?', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="block font-semibold text-sm text-slate-900 dark:text-white">{{ $assignment->user?->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">@if($assignment->user?->username){{ '@' . $assignment->user->username }}@else{{ $assignment->user?->email }}@endif</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 items-center">
                                            <a href="{{ route('absensi.officer.index', $kelas->id) }}" class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                                                Lihat Input
                                            </a>
                                            <form action="{{ route('wali.officer.revoke', $assignment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Cabut penugasan {{ $assignment->user?->name }} sebagai petugas absensi?')"
                                                    class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-lg hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                                    Cabut
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Alur Kerja Card --}}
                    <div class="bg-slate-900 dark:bg-slate-800 rounded-2xl p-6 text-white">
                        <h4 class="font-bold text-sm mb-4 text-emerald-400">Alur Absensi Kelas</h4>
                        <div class="space-y-3">
                            <div class="flex gap-3 items-start">
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shrink-0">1</div>
                                <div>
                                    <p class="text-xs font-semibold text-white">Wali Kelas Tugaskan Siswa</p>
                                    <p class="text-[11px] text-slate-400">Pilih siswa di halaman ini → Klik Tugaskan</p>
                                </div>
                            </div>
                            <div class="flex gap-3 items-start">
                                <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold shrink-0">2</div>
                                <div>
                                    <p class="text-xs font-semibold text-white">Siswa Input Absensi</p>
                                    <p class="text-[11px] text-slate-400">Siswa yang ditugaskan dapat menu "Input Absensi" di sidebar mereka</p>
                                </div>
                            </div>
                            <div class="flex gap-3 items-start">
                                <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shrink-0">3</div>
                                <div>
                                    <p class="text-xs font-semibold text-white">Kirim ke Wali Kelas</p>
                                    <p class="text-[11px] text-slate-400">Petugas menekan "Kirim" setelah semua siswa terisi</p>
                                </div>
                            </div>
                            <div class="flex gap-3 items-start">
                                <div class="w-6 h-6 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs font-bold shrink-0">4</div>
                                <div>
                                    <p class="text-xs font-semibold text-white">Wali Kelas Verifikasi</p>
                                    <p class="text-[11px] text-slate-400">Data dikunci & disetujui di menu Verifikasi Absensi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
