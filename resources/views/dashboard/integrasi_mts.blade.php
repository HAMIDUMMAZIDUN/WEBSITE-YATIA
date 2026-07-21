<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-slate-800 dark:text-slate-150 leading-tight">
                {{ __('Integrasi Database MTs') }}
            </h2>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $connected ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/25' }}">
                    {{ $connected ? 'Status: Terhubung' : 'Status: Terputus' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Session Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 rounded-2xl flex items-center gap-3 shadow-sm transition-all duration-300">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-300 rounded-2xl flex items-center gap-3 shadow-sm transition-all duration-300">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Connection Diagnostics -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-xl {{ $connected ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white">Konektivitas Database Sumber</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Aplikasi ini dikonfigurasi untuk dapat mengambil, menyinkronkan, dan merekonsiliasi data secara langsung dari database 
                            <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-xs">mts</code> pada instansi MySQL yang sama.
                        </p>
                    </div>
                </div>

                @if(!$connected)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-300 rounded-xl text-xs space-y-2">
                        <span class="font-bold block">Detail Kesalahan Koneksi:</span>
                        <code class="block font-mono bg-white dark:bg-slate-950 p-2.5 rounded border border-amber-300/50 overflow-x-auto">{{ $errorMessage }}</code>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">Driver</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">MySQL</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">Database</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">mts</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">Host / Port</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">127.0.0.1:3306</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase">Enkripsi Password</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Bcrypt (Kompatibel)</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sync Grid -->
            @if($connected)
                <div class="grid md:grid-cols-2 gap-6">
                    
                    <!-- 1. Mata Pelajaran Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div class="p-3 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $externalCounts['mapel'] === $localCounts['mapel'] ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $externalCounts['mapel'] === $localCounts['mapel'] ? 'Sinkron' : 'Perbedaan Data' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-base text-slate-800 dark:text-white">Mata Pelajaran (e_mapel)</h4>
                                <p class="text-xs text-slate-400">Sinkronisasi daftar mata pelajaran legal dari kurikulum MTs.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Di DB MTs</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $externalCounts['mapel'] }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Lokal Aktif</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $localCounts['mapel'] }}</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.integrasi.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="mapel">
                            <button type="submit" class="w-full py-2.5 bg-slate-950 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-colors shadow">
                                Sinkronkan Mata Pelajaran
                            </button>
                        </form>
                    </div>

                    <!-- 2. Data Kelas Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div class="p-3 bg-violet-500/10 text-violet-600 dark:text-violet-400 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $externalCounts['kelas'] === $localCounts['kelas'] ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $externalCounts['kelas'] === $localCounts['kelas'] ? 'Sinkron' : 'Perbedaan Data' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-base text-slate-800 dark:text-white">Data Kelas (e_kelas)</h4>
                                <p class="text-xs text-slate-400">Sinkronisasi rombongan belajar kelas serta penugasan Wali Kelas.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Di DB MTs</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $externalCounts['kelas'] }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Lokal Aktif</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $localCounts['kelas'] }}</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.integrasi.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="kelas">
                            <button type="submit" class="w-full py-2.5 bg-slate-950 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-colors shadow">
                                Sinkronkan Kelas & Wali Kelas
                            </button>
                        </form>
                    </div>

                    <!-- 3. Data Guru Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div class="p-3 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $externalCounts['guru'] === $localCounts['guru'] ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $externalCounts['guru'] === $localCounts['guru'] ? 'Sinkron' : 'Perbedaan Data' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-base text-slate-800 dark:text-white">Data Guru & Pendidik (e_guru)</h4>
                                <p class="text-xs text-slate-400">Sinkronisasi data profil guru dan otomatis pembuatan akun pengguna login.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Di DB MTs</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $externalCounts['guru'] }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Lokal Aktif</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $localCounts['guru'] }}</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.integrasi.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="guru">
                            <button type="submit" class="w-full py-2.5 bg-slate-950 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-colors shadow">
                                Sinkronkan Guru & Buat Akun
                            </button>
                        </form>
                    </div>

                    <!-- 4. Data Siswa Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $externalCounts['siswa'] === $localCounts['siswa'] ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $externalCounts['siswa'] === $localCounts['siswa'] ? 'Sinkron' : 'Perbedaan Data' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-base text-slate-800 dark:text-white">Data Siswa Aktif (e_siswa)</h4>
                                <p class="text-xs text-slate-400">Sinkronisasi data profil siswa, pemetaan kelas, dan pembuatan akun siswa.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Di DB MTs</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $externalCounts['siswa'] }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Lokal Aktif</span>
                                    <span class="text-xl font-extrabold text-slate-800 dark:text-slate-200">{{ $localCounts['siswa'] }}</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.integrasi.sync') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="siswa">
                            <button type="submit" class="w-full py-2.5 bg-slate-950 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-colors shadow">
                                Sinkronkan Siswa & Buat Akun
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Sync All Card -->
                <div class="bg-gradient-to-r from-emerald-650 to-teal-600 dark:from-emerald-950/40 dark:to-teal-950/40 rounded-3xl p-8 border border-emerald-500/20 text-white shadow-lg space-y-6">
                    <div class="max-w-2xl space-y-2">
                        <h3 class="text-xl font-black">Sinkronisasi Menyeluruh (Semua Data)</h3>
                        <p class="text-sm opacity-90 leading-relaxed">
                            Menjalankan proses sinkronisasi secara sekuensial mulai dari mata pelajaran, guru, kelas, hingga siswa dalam satu kali transaksi. 
                            Ini disarankan saat pertama kali inisialisasi aplikasi dengan database MTs.
                        </p>
                    </div>
                    <form action="{{ route('admin.integrasi.sync') }}" method="POST" onsubmit="return confirm('Proses sinkronisasi menyeluruh akan memakan waktu hingga beberapa menit. Apakah Anda yakin ingin melanjutkan?')">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="px-8 py-3.5 bg-white text-slate-900 font-extrabold rounded-2xl shadow-xl hover:bg-slate-50 transition-colors text-sm">
                            Mulai Sinkronisasi Menyeluruh
                        </button>
                    </form>
                </div>

                <!-- Log History -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white">Riwayat Aktivitas Sinkronisasi</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4">Operator</th>
                                    <th class="py-3 px-4">Tindakan</th>
                                    <th class="py-3 px-4">Modul / Entitas</th>
                                    <th class="py-3 px-4">Hasil Sinkronisasi</th>
                                    <th class="py-3 px-4">Waktu Eksekusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSyncs as $log)
                                    <tr class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 text-slate-600 dark:text-slate-350">
                                        <td class="py-3.5 px-4 font-semibold">{{ $log->user->name ?? 'System' }}</td>
                                        <td class="py-3.5 px-4">{{ $log->action }}</td>
                                        <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">{{ $log->entity }}</td>
                                        <td class="py-3.5 px-4">
                                            @if($log->new_value)
                                                @php
                                                    $details = json_decode($log->new_value, true);
                                                @endphp
                                                @if(is_array($details))
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach($details as $k => $v)
                                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] font-bold rounded-md">
                                                                {{ $k }}: +{{ $v }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 font-italic">No details</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 font-italic">No details</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-400">{{ \Carbon\Carbon::parse($log->timestamp)->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic">Belum ada riwayat aktivitas sinkronisasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
