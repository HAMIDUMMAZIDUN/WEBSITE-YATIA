<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - SIM MADRASAH ALIYAH MATHLAUL ANWAR PAMEUNTASAN</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Inline script to prevent theme flash
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        window.bulkDeleteSelected = async function(checkboxSelector, csrfToken) {
            const checkboxes = document.querySelectorAll(checkboxSelector + ':checked');
            if (checkboxes.length === 0) {
                alert('Pilih setidaknya satu data untuk dihapus.');
                return;
            }
            if (!confirm(`Apakah Anda yakin ingin menghapus ${checkboxes.length} data yang dipilih?`)) {
                return;
            }
            
            const loader = document.createElement('div');
            loader.className = 'fixed inset-0 z-[999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center';
            loader.innerHTML = `
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-3 border border-slate-100 dark:border-slate-800">
                    <svg class="animate-spin h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Menghapus data masal... <span id="bulk-progress">0</span>/${checkboxes.length}</span>
                </div>
            `;
            document.body.appendChild(loader);
            
            let deletedCount = 0;
            for (const cb of checkboxes) {
                const deleteUrl = cb.getAttribute('data-delete-url');
                if (!deleteUrl) continue;
                
                try {
                    const response = await fetch(deleteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });
                    if (response.ok) {
                        deletedCount++;
                        const progressEl = document.getElementById('bulk-progress');
                        if (progressEl) progressEl.innerText = deletedCount;
                    }
                } catch (err) {
                    console.error('Failed to delete item', err);
                }
            }
            
            window.location.reload();
        };
    </script>

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        .font-display {
            font-family: 'Outfit', sans-serif;
        }

        /* Sidebar sticky works because outer div is min-h-screen flex row */
        /* Thin scrollbar for sidebar nav */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        .dark .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #334155;
        }

        /* Modern Colorful Card Accents */
        .bg-white {
            background-color: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.015), 0 8px 10px -6px rgba(0, 0, 0, 0.015) !important;
            border-radius: 1.25rem !important;
        }
        .dark .bg-slate-900 {
            background-color: #0f172a !important; /* Space navy */
            border: 1px solid rgba(51, 65, 85, 0.5) !important;
            box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.15) !important;
            border-radius: 1.25rem !important;
        }

        /* Soft colorful gradient glows globally */
        body {
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.04) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.03) 0%, transparent 40%),
                        #f8fafc !important;
        }
        html.dark body {
            background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.06) 0%, transparent 45%),
                        radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.04) 0%, transparent 45%),
                        #020617 !important; /* Ultra-premium dark slate */
        }

        /* Neon / Stabilo active menu styling */
        .active-stabilo {
            background-color: #d9f99d !important; /* Lime 200 */
            color: #0f172a !important; /* Dark text */
            font-weight: 800 !important;
            border: 1px solid #a3e635 !important; /* Lime 400 */
            box-shadow: 0 4px 12px 0 rgba(163, 230, 53, 0.25) !important;
        }
        .dark .active-stabilo {
            background-color: #e6ff00 !important; /* Neon stabilo yellow */
            color: #020617 !important;
            font-weight: 900 !important;
            border: 1px solid #bef264 !important;
            box-shadow: 0 4px 20px 0 rgba(230, 255, 0, 0.35) !important;
        }
    </style>
</head>

<body x-data="{ 
        sidebarOpen: window.innerWidth >= 768 ? (localStorage.getItem('sidebarOpen') !== 'false') : false,
        activeHash: (function(){ var p='{{ request()->path() }}'; if(p.includes('profile')) return '#profile'; if(p.includes('admin/users')) return '#users-section'; if(p.includes('admin/kelas')) return '#kelas'; if(p.includes('admin/mapel')) return '#mapel'; if(p.includes('wali/siswa')) return '#wali-siswa'; if(p.includes('wali/absensi')) return '#wali-absensi'; if(p.includes('guru/jadwal')) return '#jadwal-mengajar'; if(p.includes('guru/elearning')) return '#elearning-section'; if(p.includes('guru/nilai')) return '#nilai-section'; if(p.includes('bk/pelanggaran')) return '#kasus-section'; if(p.includes('siswa/elearning')) return '#siswa-elearning'; if(p.includes('siswa/cbt')) return '#siswa-cbt'; if(p.includes('siswa/jadwal')) return '#siswa-jadwal'; if(p.includes('siswa/presensi')) return '#siswa-presensi'; return '#dashboard'; })(),
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            if (window.innerWidth >= 768) {
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
            }
        }
    }" @hashchange.window="activeHash = window.location.hash || '#dashboard'"
    class="font-sans antialiased text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    {{-- Outer flex container: sidebar + content sit side by side --}}
    <div class="flex min-h-screen bg-slate-50/50 dark:bg-slate-950">

        {{-- ─── DESKTOP SIDEBAR: sticky so it stays visible on scroll ───── --}}
        {{-- flex-row sibling so it NEVER overlaps the main content area       --}}
        <aside @click.stop class="hidden md:flex flex-col shrink-0 sticky top-0 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 z-30 transition-all duration-300 ease-in-out overflow-hidden"
            :style="sidebarOpen ? 'width: 16rem; min-width: 16rem;' : 'width: 5rem; min-width: 5rem;'">
            {{-- Inner wrapper dynamically sizes to parent width --}}
            <div class="w-full flex flex-col h-full overflow-y-auto sidebar-scroll">
                @include('layouts.navigation.desktop')
            </div>
        </aside>

        {{-- ─── MOBILE SIDEBAR OVERLAY ───────────────────────────────────── --}}
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="sidebarOpen = false"></div>
            {{-- Drawer --}}
            <aside x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                @click.stop
                class="fixed inset-y-0 left-0 w-64 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 z-50 transform">
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-9 w-auto text-emerald-600" />
                        <span class="font-display font-extrabold text-base text-slate-900 dark:text-white">SIMS YATIA</span>
                    </div>
                    <button @click="sidebarOpen = false" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @include('layouts.navigation.desktop')
                </div>
            </aside>
        </div>

        {{-- ─── MAIN CONTENT AREA ────────────────────────────────────────── --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- Top Navbar --}}
            <div class="sticky top-0 z-20 flex h-16 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    {{-- Hamburger --}}
                    <button @click="toggleSidebar()" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Branding shown when sidebar is closed --}}
                    <div class="flex items-center gap-2"
                        x-show="!sidebarOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <x-application-logo class="h-8 w-auto text-emerald-600 dark:text-emerald-450" />
                        <span class="font-display font-extrabold text-base tracking-tight text-slate-900 dark:text-white leading-none">SIMS YATIA</span>
                    </div>
                </div>

                {{-- Right: user badge --}}
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2.5">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Selamat datang,</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="px-2.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] uppercase tracking-wider">{{ Auth::user()->role->display_name }}</span>
                    </div>
                </div>
            </div>

            {{-- Page Heading --}}
            @isset($header)
            <header class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/80 shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            {{-- Page Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>