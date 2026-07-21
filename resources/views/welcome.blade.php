<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Sistem Informasi Manajemen Sekolah (SIMS) Terintegrasi YATIA - Administrasi sekolah modern, aman, dan mudah digunakan.">
        <title>SIMS YATIA - Portal Sekolah Terintegrasi</title>

        <!-- Google Fonts: Outfit & Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & JS from Laravel Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Inline script to prevent theme flash
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, .font-display {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 antialiased min-h-screen flex flex-col transition-colors duration-300">
        <!-- Navigation -->
        <nav class="bg-white/80 dark:bg-slate-950/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 dark:border-slate-800/80 shadow-sm transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center gap-3">
                        <!-- School Logo / Branding -->
                        <div class="w-12 h-12 text-emerald-600 dark:text-emerald-450 shrink-0">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white font-display">SIMS YATIA</span>
                            <span class="block text-xs text-emerald-600 dark:text-emerald-400 font-semibold tracking-wider uppercase">Terintegrasi</span>
                        </div>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8 h-full">
                        <a href="#home" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Home</a>
                        <a href="#berita" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Berita & Agenda</a>
                        <a href="#galeri" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Galeri</a>
                        <a href="#kontak" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Kontak</a>
                    </div>
 
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle Button -->
                        <button onclick="toggleDarkMode()" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <!-- Sun Icon -->
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                            <!-- Moon Icon -->
                            <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>

                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all duration-200 hover:-translate-y-0.5">
                                    Ke Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all duration-200 hover:-translate-y-0.5">
                                    Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="relative overflow-hidden bg-cover bg-center bg-no-repeat py-20 md:py-28 border-b border-slate-100 dark:border-slate-850" style="background-image: url('{{ asset('images/bg-school.jpg') }}');">
            <!-- Backdrop Overlay -->
            <div class="absolute inset-0 bg-slate-950/70 dark:bg-slate-950/85 backdrop-blur-[2px] pointer-events-none"></div>
            
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center py-8">
                <div class="space-y-8">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white font-display leading-tight">
                        Platform Digital Administrasi & Akademik <span class="text-emerald-400">YATIA</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-200 max-w-2xl mx-auto leading-relaxed">
                        Menghubungkan Super Admin, Guru, Wali Kelas, Siswa, dan Guru BK dalam satu ekosistem administrasi sekolah terpadu, modern, aman, dan mudah digunakan.
                    </p>
                    <div class="pt-2 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-555 text-white rounded-xl text-base font-bold shadow-lg shadow-emerald-600/15 transition-all duration-200 hover:-translate-y-0.5">
                            Masuk Sistem
                        </a>
                        <a href="#fitur" class="px-8 py-4 bg-white/10 hover:bg-white/20 rounded-xl text-base font-bold border border-white/20 text-white transition-all duration-200">
                            Pelajari Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-white dark:bg-slate-900 border-y border-slate-100 dark:border-slate-800 py-10 shadow-inner transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <span class="block text-4xl font-extrabold text-slate-900 dark:text-white font-display">5+</span>
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Peran Pengguna</span>
                    </div>
                    <div>
                        <span class="block text-4xl font-extrabold text-slate-900 dark:text-white font-display">100%</span>
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terintegrasi</span>
                    </div>
                    <div>
                        <span class="block text-4xl font-extrabold text-slate-900 dark:text-white font-display">Realtime</span>
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Absensi & Nilai</span>
                    </div>
                    <div>
                        <span class="block text-4xl font-extrabold text-slate-900 dark:text-white font-display">Aman</span>
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">RBAC + Middleware</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Roles Section -->
        <section id="fitur" class="py-20 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-950 dark:text-white font-display">Ekosistem Hak Akses Terpadu (RBAC)</h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    Aplikasi ini dirancang untuk mendistribusikan wewenang secara presisi sesuai dengan tugas operasional masing-masing civitas akademika.
                </p>
                <div class="grid md:grid-cols-5 gap-6 pt-10">
                    <!-- Super Admin -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md dark:shadow-none transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center font-bold text-xl mb-4">SA</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Super Admin</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-405">Akses penuh, CRUD data master pengguna, kelas, kurikulum, dan ekspor laporan global.</p>
                    </div>
                    <!-- Guru Mapel -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md dark:shadow-none transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 rounded-xl flex items-center justify-center font-bold text-xl mb-4">GM</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Guru Mapel</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-405">Kelola jadwal, absensi kelas, materi/tugas e-learning, CBT, dan nilai Kurikulum Merdeka.</p>
                    </div>
                    <!-- Wali Kelas -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md dark:shadow-none transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded-xl flex items-center justify-center font-bold text-xl mb-4">WK</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Wali Kelas</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-405">Administrasi satu kelas binaan, rekap presensi, catatan perkembangan, dan cetak e-Rapor otomatis.</p>
                    </div>
                    <!-- Siswa -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md dark:shadow-none transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 rounded-xl flex items-center justify-center font-bold text-xl mb-4">SW</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Siswa</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-405">Akses materi belajar, kumpulkan tugas, ikuti ujian online CBT, rekap presensi, dan e-Rapor.</p>
                    </div>
                    <!-- Guru BK -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md dark:shadow-none transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 rounded-xl flex items-center justify-center font-bold text-xl mb-4">BK</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Guru BK</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-405">Catat kasus & sistem poin kedisiplinan, prestasi siswa, jurnal konseling, dan surat panggilan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- News & Agenda Section -->
        <section id="berita" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
                <div class="flex justify-between items-end">
                    <div class="space-y-2">
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-950 dark:text-white font-display">Berita & Pengumuman Sekolah</h2>
                        <p class="text-slate-600 dark:text-slate-400 max-w-xl">Ikuti terus informasi penting mengenai kegiatan, agenda akademik, dan prestasi terhangat sekolah.</p>
                    </div>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline cursor-pointer hidden md:inline-flex items-center gap-1 text-sm">
                        Lihat Semua Berita
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
                <div class="grid md:grid-cols-3 gap-8 pt-8">
                    <!-- News Card 1 -->
                    <article class="bg-white dark:bg-slate-950 rounded-2xl overflow-hidden border border-slate-200/60 dark:border-slate-850 group hover:shadow-lg dark:hover:shadow-none hover:border-emerald-200 dark:hover:border-emerald-900 transition-all duration-300">
                        <div class="h-48 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 border-b border-slate-100 dark:border-slate-800 relative overflow-hidden flex items-center justify-center">
                            <span class="absolute top-4 left-4 z-20 px-3 py-1 rounded-lg bg-emerald-600 text-white font-semibold text-xs uppercase tracking-wider">Akademik</span>
                            <div class="text-slate-400 dark:text-slate-500 flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-emerald-600/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span class="text-xs font-bold font-display uppercase tracking-wider text-slate-500 dark:text-slate-400">Kurikulum Merdeka</span>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold block">20 Juli 2026</span>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                Persiapan Implementasi Kurikulum Merdeka Tahun Ajaran Baru
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3">
                                Rapat koordinasi guru menyepakati penyesuaian modul ajar dan sistem penilaian Kurikulum Merdeka terintegrasi di SIMS YATIA.
                            </p>
                        </div>
                    </article>

                    <!-- News Card 2 -->
                    <article class="bg-white dark:bg-slate-950 rounded-2xl overflow-hidden border border-slate-200/60 dark:border-slate-850 group hover:shadow-lg dark:hover:shadow-none hover:border-amber-200 dark:hover:border-amber-900 transition-all duration-300">
                        <div class="h-48 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 border-b border-slate-100 dark:border-slate-800 relative overflow-hidden flex items-center justify-center">
                            <span class="absolute top-4 left-4 z-20 px-3 py-1 rounded-lg bg-amber-600 text-white font-semibold text-xs uppercase tracking-wider">Prestasi</span>
                            <div class="text-slate-400 dark:text-slate-500 flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-amber-600/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                <span class="text-xs font-bold font-display uppercase tracking-wider text-slate-500 dark:text-slate-400">Olimpiade Sains</span>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold block">18 Juli 2026</span>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                Siswa YATIA Raih Juara 1 Olimpiade Fisika Tingkat Nasional
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3">
                                Selamat kepada tim Fisika sekolah atas pencapaian gemilang. Apresiasi besar diberikan untuk dedikasi siswa dan guru pembimbing.
                            </p>
                        </div>
                    </article>

                    <!-- News Card 3 -->
                    <article class="bg-white dark:bg-slate-950 rounded-2xl overflow-hidden border border-slate-200/60 dark:border-slate-850 group hover:shadow-lg dark:hover:shadow-none hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-300">
                        <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 border-b border-slate-100 dark:border-slate-800 relative overflow-hidden flex items-center justify-center">
                            <span class="absolute top-4 left-4 z-20 px-3 py-1 rounded-lg bg-blue-600 text-white font-semibold text-xs uppercase tracking-wider">Agenda</span>
                            <div class="text-slate-400 dark:text-slate-500 flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-blue-600/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-bold font-display uppercase tracking-wider text-slate-500 dark:text-slate-400">Rencana Akademik</span>
                            </div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold block">15 Juli 2026</span>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                Jadwal Pengisian Rencana Pembelajaran Semester Ganjil
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3">
                                Diharapkan seluruh guru mata pelajaran untuk melengkapi jadwal dan silabus materi pelajaran di portal masing-masing sebelum 1 Agustus.
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- School Gallery Section -->
        <section id="galeri" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-950 dark:text-white font-display">Galeri Sekolah</h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Momen dan dokumentasi visual aktivitas belajar, fasilitas modern, serta keceriaan civitas akademika.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-10">
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 flex flex-col items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs p-4 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all duration-300 hover:shadow-md dark:hover:shadow-none">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Ruang Kelas Modern
                    </div>
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/20 dark:to-indigo-950/20 flex flex-col items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs p-4 hover:border-blue-300 dark:hover:border-blue-800 transition-all duration-300 hover:shadow-md dark:hover:shadow-none">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"/></svg>
                        Laboratorium IPA
                    </div>
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 flex flex-col items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs p-4 hover:border-amber-300 dark:hover:border-amber-800 transition-all duration-300 hover:shadow-md dark:hover:shadow-none">
                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Perpustakaan Digital
                    </div>
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-950/20 dark:to-red-950/20 flex flex-col items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs p-4 hover:border-rose-300 dark:hover:border-rose-800 transition-all duration-300 hover:shadow-md dark:hover:shadow-none">
                        <svg class="w-8 h-8 text-rose-600 dark:text-rose-455 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V8a2 2 0 00-2-2h-1.5a2 2 0 01-2-2v-.5M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                        Fasilitas Olahraga
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="kontak" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-12 gap-12 items-start">
                    <div class="md:col-span-5 space-y-6">
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-950 dark:text-white font-display">Hubungi Kami</h2>
                        <p class="text-slate-600 dark:text-slate-400">Punya pertanyaan seputar kurikulum, sistem SIMS, atau pendaftaran sekolah? Tim kami siap melayani Anda.</p>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-950 dark:text-slate-200">Alamat Sekolah</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">Jl. Pembangunan No. 12, Senayan, Jakarta Pusat</span>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-950 dark:text-slate-200">Telepon & Email</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">+62 (21) 555-0123 / info@yatia.sch.id</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:col-span-7 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-8 shadow-sm dark:shadow-none">
                        <h3 class="text-xl font-bold text-slate-950 dark:text-white mb-4 font-display">Kirim Pesan</h3>
                        <form class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                    <input type="text" placeholder="Masukkan nama Anda" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat Email</label>
                                    <input type="email" placeholder="contoh@domain.com" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subjek</label>
                                <input type="text" placeholder="Topik pertanyaan" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pesan Anda</label>
                                <textarea rows="4" placeholder="Tuliskan detail pertanyaan Anda..." class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-none"></textarea>
                            </div>
                            <button type="button" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/10 dark:shadow-none transition-all duration-200">
                                Kirim Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 border-t border-slate-800 py-12 mt-auto text-slate-400 text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain" alt="Logo YATIA">
                        <span class="text-white font-bold font-display">SIMS YATIA</span>
                    </div>
                    <span>&copy; 2026 Sekolah YATIA. Hak Cipta Dilindungi Undang-Undang.</span>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-emerald-500">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-emerald-500">Syarat Ketentuan</a>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            function toggleDarkMode() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            }

            // Scrollspy logic to highlight active navbar section
            document.addEventListener('DOMContentLoaded', () => {
                const sections = document.querySelectorAll('section[id]');
                const navLinks = document.querySelectorAll('.nav-link');

                function scrollSpy() {
                    let current = 'home';
                    const scrollPos = window.scrollY + 120; // Offset for navbar height + buffer

                    sections.forEach(section => {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.offsetHeight;
                        if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                            current = section.getAttribute('id');
                        }
                    });

                    // Special case: if scrolled near the bottom, highlight the last section (kontak)
                    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
                        current = 'kontak';
                    }

                    navLinks.forEach(link => {
                        link.classList.remove(
                            'border-emerald-600', 'text-emerald-600', 'dark:border-emerald-400', 'dark:text-emerald-450', 'font-semibold',
                            'dark:text-emerald-400'
                        );
                        link.classList.add('border-transparent', 'text-slate-500', 'dark:text-slate-400');
                        
                        if (link.getAttribute('href') === `#${current}`) {
                            link.classList.remove('border-transparent', 'text-slate-500', 'dark:text-slate-400');
                            link.classList.add('border-emerald-600', 'text-emerald-600', 'dark:border-emerald-400', 'dark:text-emerald-400', 'font-semibold');
                        }
                    });
                }

                window.addEventListener('scroll', scrollSpy);
                scrollSpy(); // Run once initially
            });
        </script>
    </body>
</html>
