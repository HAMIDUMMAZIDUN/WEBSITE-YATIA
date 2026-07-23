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

            <!-- Dashboard Administrator Section (Full Width) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm dark:shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col gap-6 transition-colors duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Dashboard Administrator</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                            Status pengisian absensi hari ini: {{ $classes_filled_count }} dari {{ $classes_total_count }} kelas selesai mengisi
                        </p>
                    </div>
                </div>

                <!-- Filter Form -->
                <form id="filter-form" method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-4 bg-slate-50/50 dark:bg-slate-800/30 p-4 rounded-2xl border border-slate-150/80 dark:border-slate-800">
                    <!-- Unit Selector -->
                    <div class="relative flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl px-3 py-2 text-sm text-slate-700 dark:text-slate-200 w-full sm:w-auto shadow-sm">
                        <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <select name="unit" onchange="this.form.submit()" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold pr-8 cursor-pointer dark:bg-slate-900">
                            <option value="MA" {{ $selectedUnit === 'MA' ? 'selected' : '' }}>Aliyah</option>
                            <option value="MTs" {{ $selectedUnit === 'MTs' ? 'selected' : '' }}>MTS</option>
                        </select>
                    </div>

                    <!-- Classes Multiselect (Alpine.js) -->
                    <div x-data="{ 
                        open: false, 
                        selectedClasses: @js($selectedClassIds),
                        allClasses: @js($kelas->pluck('id')->toArray()),
                        get label() {
                            if (this.selectedClasses.length === 0) return 'Pilih Kelas';
                            if (this.selectedClasses.length === this.allClasses.length) return 'Semua kelas terpilih';
                            return this.selectedClasses.length + ' kelas terpilih';
                        },
                        toggleAll() {
                            if (this.selectedClasses.length === this.allClasses.length) {
                                this.selectedClasses = [];
                            } else {
                                this.selectedClasses = [...this.allClasses];
                            }
                        }
                    }" class="relative w-full sm:w-auto">
                        <button @click="open = !open" type="button" class="w-full sm:w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl px-3 py-2 text-sm flex items-center justify-between text-slate-700 dark:text-slate-200 hover:border-slate-350 transition-colors shadow-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="label"></span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 right-0 z-30 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl shadow-xl max-h-60 overflow-y-auto p-2" style="display:none">
                            <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-2">
                                <button @click="toggleAll()" type="button" class="w-full text-left px-2 py-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg">
                                    <span x-text="selectedClasses.length === allClasses.length ? 'Hapus Semua Pilihan' : 'Pilih Semua Kelas'"></span>
                                </button>
                            </div>
                            <div class="space-y-1">
                                @foreach($kelas as $c)
                                    <label class="flex items-center px-2 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg cursor-pointer text-sm text-slate-700 dark:text-slate-200">
                                        <input type="checkbox" name="kelas_ids[]" value="{{ $c->id }}" x-model="selectedClasses" class="rounded border-slate-300 dark:border-slate-750 text-emerald-600 focus:ring-emerald-500 mr-2">
                                        <span>{{ $c->nama_kelas }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Datepicker -->
                    <div class="relative flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl px-3 py-2 text-sm text-slate-700 dark:text-slate-200 w-full sm:w-auto shadow-sm">
                        <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="date" name="date" value="{{ $date }}" class="bg-transparent border-none p-0 focus:ring-0 text-sm font-semibold cursor-pointer dark:bg-slate-900">
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-slate-900 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-750 text-white text-sm font-bold rounded-xl shadow-md transition-all hover:scale-[1.02] cursor-pointer">
                        Cari
                    </button>

                    <!-- Exports on the Right -->
                    <div class="sm:ml-auto flex gap-2 w-full sm:w-auto justify-end">
                        <button type="button" onclick="exportData('pdf')" class="px-4 py-2 border border-slate-200 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer bg-white dark:bg-slate-900 shadow-sm">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            PDF
                        </button>
                        <button type="button" onclick="exportData('excel')" class="px-4 py-2 border border-slate-200 dark:border-slate-750 hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors cursor-pointer bg-white dark:bg-slate-900 shadow-sm">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Excel
                        </button>
                    </div>
                </form>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Total Siswa -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-150 dark:border-slate-800 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Siswa</span>
                            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $attendance_stats['total_siswa'] }}</span>
                        </div>
                    </div>

                    <!-- Hadir -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border-l-4 border-l-emerald-500 border-slate-200/60 dark:border-slate-800/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Hadir</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $attendance_stats['hadir'] }}</span>
                        </div>
                    </div>

                    <!-- Sakit -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border-l-4 border-l-amber-500 border-slate-200/60 dark:border-slate-800/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Sakit</span>
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $attendance_stats['sakit'] }}</span>
                        </div>
                    </div>

                    <!-- Izin -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border-l-4 border-l-blue-500 border-slate-200/60 dark:border-slate-800/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Izin</span>
                            <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $attendance_stats['izin'] }}</span>
                        </div>
                    </div>

                    <!-- Alpa -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border-l-4 border-l-rose-500 border-slate-200/60 dark:border-slate-800/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Alpa</span>
                            <span class="text-2xl font-black text-rose-600 dark:text-rose-450">{{ $attendance_stats['alpa'] }}</span>
                        </div>
                    </div>

                    <!-- Kehadiran -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border-l-4 border-l-purple-500 border-slate-200/60 dark:border-slate-800/80 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Kehadiran</span>
                            <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">{{ number_format($attendance_stats['kehadiran_pct'], 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Table of Classes Rekapitulasi -->
                <div>
                    <h4 class="text-sm font-bold text-slate-850 dark:text-slate-200 mb-3.5">Rekapitulasi Kehadiran Harian per Kelas</h4>
                    <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800 shadow-inner">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4">Kelas</th>
                                    <th class="py-3 px-4 text-center">Total Siswa</th>
                                    <th class="py-3 px-4 text-center">Hadir</th>
                                    <th class="py-3 px-4 text-center">Sakit</th>
                                    <th class="py-3 px-4 text-center">Izin</th>
                                    <th class="py-3 px-4 text-center">Alpa</th>
                                    <th class="py-3 px-4 text-right">Kehadiran (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                                @forelse($tableData as $row)
                                    <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-800/10 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $row['nama_kelas'] }}</td>
                                        <td class="py-3 px-4 text-center text-slate-500 dark:text-slate-400 font-semibold">{{ $row['total_siswa'] }}</td>
                                        <td class="py-3 px-4 text-center text-emerald-600 dark:text-emerald-455 font-bold">{{ $row['hadir'] }}</td>
                                        <td class="py-3 px-4 text-center text-amber-600 dark:text-amber-450 font-bold">{{ $row['sakit'] }}</td>
                                        <td class="py-3 px-4 text-center text-blue-600 dark:text-blue-450 font-bold">{{ $row['izin'] }}</td>
                                        <td class="py-3 px-4 text-center text-rose-600 dark:text-rose-450 font-bold">{{ $row['alpa'] }}</td>
                                        <td class="py-3 px-4 text-right text-slate-900 dark:text-white font-bold font-mono">
                                            {{ number_format($row['kehadiran_pct'], 1) }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 italic">Tidak ada kelas terpilih atau data tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($tableData) > 0)
                                <tfoot>
                                    <tr class="bg-emerald-50/20 dark:bg-emerald-950/10 font-bold border-t border-slate-200 dark:border-slate-800">
                                        <td class="py-3.5 px-4 text-slate-900 dark:text-white">TOTAL</td>
                                        <td class="py-3.5 px-4 text-center text-slate-700 dark:text-slate-300">{{ $totals['total_siswa'] }}</td>
                                        <td class="py-3.5 px-4 text-center text-emerald-600 dark:text-emerald-400">{{ $totals['hadir'] }}</td>
                                        <td class="py-3.5 px-4 text-center text-amber-600 dark:text-amber-400">{{ $totals['sakit'] }}</td>
                                        <td class="py-3.5 px-4 text-center text-blue-600 dark:text-blue-400">{{ $totals['izin'] }}</td>
                                        <td class="py-3.5 px-4 text-center text-rose-600 dark:text-rose-450">{{ $totals['alpa'] }}</td>
                                        <td class="py-3.5 px-4 text-right text-slate-900 dark:text-white font-mono">
                                            {{ number_format($totals['kehadiran_pct'], 1) }}%
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Recent Activities (Expanded Width for better layout) -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-lg border border-slate-100 dark:border-slate-800/60 transition-colors duration-300">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-4">Aktivitas Terbaru</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[300px] overflow-y-auto pr-1">
                    @forelse($latest_activities as $activity)
                        @php
                            $actorName = $activity->user ? $activity->user->name : 'Sistem';
                            $initial = strtoupper(substr($actorName, 0, 1));
                            // Choose avatar color dynamically
                            $colorClasses = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-350';
                            if ($initial === 'A') $colorClasses = 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-350';
                            if ($initial === 'G') $colorClasses = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-350';
                            if ($initial === 'B') $colorClasses = 'bg-pink-100 text-pink-800 dark:bg-pink-950/40 dark:text-pink-350';
                            if ($initial === 'H') $colorClasses = 'bg-teal-100 text-teal-800 dark:bg-teal-950/40 dark:text-teal-350';
                        @endphp
                        <div class="flex items-start gap-3 p-3 bg-slate-50/50 dark:bg-slate-850/20 rounded-2xl border border-slate-100/50 dark:border-slate-800/50 hover:shadow-sm transition-shadow">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $colorClasses }}">
                                {{ $initial }}
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $actorName }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $activity->action }}
                                </div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6 col-span-2">Tidak ada aktivitas terbaru.</p>
                    @endforelse
                </div>
            </div>

            <!-- Custom Script for Exports -->
            <script>
                function exportData(type) {
                    const form = document.getElementById('filter-form');
                    const originalAction = form.action;
                    const originalTarget = form.target;
                    
                    // Create temporary input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'export';
                    input.value = type;
                    form.appendChild(input);
                    
                    if (type === 'pdf') {
                        form.target = '_blank';
                    } else {
                        form.target = '_self';
                    }
                    
                    form.submit();
                    
                    // Cleanup with slight delay to ensure browser triggers download/print page
                    setTimeout(() => {
                        form.removeChild(input);
                        form.target = originalTarget;
                    }, 500);
                }
            </script>
        </div>
    </div>
</x-app-layout>
