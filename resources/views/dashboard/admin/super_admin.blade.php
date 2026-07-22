<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-white dark:bg-slate-900 px-6 py-4 rounded-xl shadow-sm dark:shadow-lg border border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <h2 class="font-bold text-xl text-slate-900 dark:text-white tracking-wide flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded bg-emerald-500"></span>
                {{ __('Super Admin Dashboard') }}
            </h2>
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                TA 2026/2027 • <span class="text-emerald-600 dark:text-emerald-400 font-bold">Aktif</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3 shadow-sm dark:shadow-md animate-fade-in">
                    <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-450 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Welcome Panel -->
            <div class="bg-gradient-to-br from-white via-slate-50 to-white dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 text-slate-900 dark:text-white rounded-3xl p-6 shadow-sm dark:shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 transition-colors duration-300">
                <div>
                    <h3 class="text-2xl font-extrabold tracking-tight mt-1 text-slate-900 dark:text-white">Selamat datang, Super Admin</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">
                        Ringkasan operasional madrasah hari ini
                    </p>
                </div>
                <div class="bg-slate-100/80 dark:bg-slate-900/80 px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 shrink-0 text-center flex items-center gap-4 transition-colors duration-300">
                    <span class="block text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">SERVER</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-center gap-1.5 justify-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        ONLINE
                    </span>
                </div>
            </div>

            <!-- 8 Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Card 1: Guru Laki-laki -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        L
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Guru Laki-laki</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['gurus_l'] }}</span>
                    </div>
                </div>

                <!-- Card 2: Guru Perempuan -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-purple-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        P
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Guru Perempuan</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['gurus_p'] }}</span>
                    </div>
                </div>

                <!-- Card 3: Total Guru -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        Σ
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Total Guru</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['gurus_total'] }}</span>
                    </div>
                </div>

                <!-- Card 4: Siswa Laki-laki -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        L
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Siswa Laki-laki</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['siswas_l'] }}</span>
                    </div>
                </div>

                <!-- Card 5: Siswa Perempuan -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-pink-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        P
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Siswa Perempuan</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['siswas_p'] }}</span>
                    </div>
                </div>

                <!-- Card 6: Total Siswa -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        Σ
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Total Siswa</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['siswas_total'] }}</span>
                    </div>
                </div>

                <!-- Card 7: Jumlah Kelas -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        K
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Jumlah Kelas</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['kelas_total'] }}</span>
                    </div>
                </div>

                <!-- Card 8: Mata Pelajaran -->
                <div class="bg-white p-5 rounded-2xl shadow-md border border-slate-100 flex items-center gap-4 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-red-500 text-white flex items-center justify-center font-extrabold text-lg shadow-inner">
                        M
                    </div>
                    <div>
                        <span class="block text-slate-500 text-xs font-bold uppercase tracking-wider">Mata Pelajaran</span>
                        <span class="text-3xl font-extrabold text-slate-950">{{ $counts['mapel_total'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Row Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Absensi Siswa Hari Ini (8/12 columns) -->
                <div class="lg:col-span-8 bg-white p-6 rounded-3xl shadow-lg border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Absensi Siswa Hari Ini</h3>
                                <p class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} • {{ $classes_filled_count }} dari {{ $classes_total_count }} kelas sudah mengisi
                                </p>
                            </div>
                        </div>

                        <!-- Progress / Count Blocks -->
                        <div class="grid grid-cols-5 gap-3 mb-6">
                            <!-- Hadir -->
                            <div class="p-3 bg-slate-50 border-l-4 border-emerald-500 rounded-lg">
                                <span class="block text-[10px] uppercase font-bold text-slate-500">Hadir</span>
                                <span class="text-lg font-black text-slate-900">{{ $attendance_stats['hadir'] }}</span>
                            </div>
                            <!-- Sakit -->
                            <div class="p-3 bg-slate-50 border-l-4 border-blue-500 rounded-lg">
                                <span class="block text-[10px] uppercase font-bold text-slate-500">Sakit</span>
                                <span class="text-lg font-black text-slate-900">{{ $attendance_stats['sakit'] }}</span>
                            </div>
                            <!-- Izin -->
                            <div class="p-3 bg-slate-50 border-l-4 border-amber-500 rounded-lg">
                                <span class="block text-[10px] uppercase font-bold text-slate-500">Izin</span>
                                <span class="text-lg font-black text-slate-900">{{ $attendance_stats['izin'] }}</span>
                            </div>
                            <!-- Alpa -->
                            <div class="p-3 bg-slate-50 border-l-4 border-rose-500 rounded-lg">
                                <span class="block text-[10px] uppercase font-bold text-slate-500">Alpa</span>
                                <span class="text-lg font-black text-slate-900">{{ $attendance_stats['alpa'] }}</span>
                            </div>
                            <!-- Terlambat -->
                            <div class="p-3 bg-slate-50 border-l-4 border-purple-500 rounded-lg">
                                <span class="block text-[10px] uppercase font-bold text-slate-500">Terlambat</span>
                                <span class="text-lg font-black text-slate-900">{{ $attendance_stats['terlambat'] }}</span>
                            </div>
                        </div>

                        <!-- Table of Classes -->
                        <div class="overflow-x-auto max-h-80 overflow-y-auto pr-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                        <th class="py-2">Kelas</th>
                                        <th class="py-2">Kehadiran</th>
                                        <th class="py-2 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-sm">
                                    @foreach($class_status_list as $cStat)
                                        <tr>
                                            <td class="py-3 font-bold text-slate-900">{{ $cStat['kelas']->nama_kelas }}</td>
                                            <td class="py-3 font-semibold text-slate-500">
                                                @if($cStat['status'] === 'Selesai')
                                                    {{ $cStat['total_students'] }}/{{ $cStat['total_students'] }}
                                                @else
                                                    0/{{ $cStat['total_students'] }}
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                @if($cStat['status'] === 'Selesai')
                                                    <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-lg text-xs font-bold shadow-sm">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 bg-rose-500 text-white rounded-lg text-xs font-bold shadow-sm animate-pulse">
                                                        Belum Isi
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Aktivitas Terbaru (4/12 columns) -->
                <div class="lg:col-span-4 bg-white p-6 rounded-3xl shadow-lg border border-slate-100 flex flex-col">
                    <h3 class="text-lg font-extrabold text-slate-900 mb-4">Aktivitas Terbaru</h3>
                    <div class="space-y-4 flex-1 overflow-y-auto max-h-[420px] pr-1">
                        @forelse($latest_activities as $activity)
                            @php
                                $actorName = $activity->user ? $activity->user->name : 'Sistem';
                                $initial = strtoupper(substr($actorName, 0, 1));
                                // Choose avatar color dynamically
                                $colorClasses = 'bg-emerald-100 text-emerald-800';
                                if ($initial === 'A') $colorClasses = 'bg-blue-100 text-blue-800';
                                if ($initial === 'G') $colorClasses = 'bg-indigo-100 text-indigo-800';
                                if ($initial === 'B') $colorClasses = 'bg-pink-100 text-pink-800';
                                if ($initial === 'H') $colorClasses = 'bg-teal-100 text-teal-800';
                            @endphp
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $colorClasses }}">
                                    {{ $initial }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900">
                                        {{ $actorName }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $activity->action }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-6">Tidak ada aktivitas terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
