<!DOCTYPE html>
<html lang="id" class="{{ (isset($school_profile) && $school_profile->theme_mode === 'dark') ? 'dark' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SIMS YATIA - Portal Masuk Sistem Informasi Manajemen Sekolah Terintegrasi.">

    <title>Masuk Sistem - SIM MADRASAH ALIYAH MATHLAUL ANWAR PAMEUNTASAN</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & JS from Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-display {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="relative text-black dark:text-white antialiased min-h-screen flex items-center justify-center p-4 transition-colors duration-300 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/bg-school.jpg') }}');">
    <!-- Backdrop Overlay -->
    <div class="absolute inset-0 bg-slate-950/75 dark:bg-slate-950/85 backdrop-blur-[3px] pointer-events-none z-0"></div>

    <!-- Floating Theme Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 shadow-md hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
            <!-- Sun Icon (visible in dark mode) -->
            <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
            </svg>
            <!-- Moon Icon (visible in light mode) -->
            <svg id="theme-toggle-dark-icon" class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <div class="w-full max-w-md space-y-6 relative z-10">
        <!-- School Emblem & Header -->
        <div class="text-center space-y-3">
            <a href="/" class="inline-block transition-transform hover:scale-105 duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center">
                    <x-application-logo class="w-full h-full object-contain" />
                </div>
            </a>
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold font-display tracking-tight text-slate-900 dark:text-white">SIMS YATIA</h2>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sistem Informasi Akademik & Kedisiplinan Terintegrasi</p>
            </div>
        </div>

        <!-- Login / Auth Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-150 dark:border-slate-800 p-8 rounded-3xl shadow-xl shadow-slate-100/50 dark:shadow-none relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer info -->
        <p class="text-center text-[11px] text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} SIMS YATIA. Hak Cipta Dilindungi Undang-Undang.
        </p>
    </div>

    <script>
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        }
    </script>
</body>

</html>