{{-- Sidebar partial — sidebarOpen & activeHash from body x-data --}}
<div class="flex flex-col h-full">

    {{-- Branding --}}
    <div class="h-16 border-b border-slate-100 dark:border-slate-800 flex items-center shrink-0 overflow-hidden"
        :class="sidebarOpen ? 'px-6 gap-3 justify-start' : 'px-0 justify-center'">
        <x-application-logo class="h-8 w-8 shrink-0 text-emerald-600 dark:text-emerald-400" />
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-150 delay-100"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <span class="font-display font-extrabold text-base text-slate-900 dark:text-white block leading-none whitespace-nowrap">SIMS YATIA</span>
            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider mt-0.5 block">Terintegrasi</span>
        </div>
    </div>

    {{-- User Avatar --}}
    <div class="border-b border-slate-100 dark:border-slate-800 flex items-center shrink-0 overflow-hidden py-4"
        :class="sidebarOpen ? 'px-6 gap-3' : 'px-0 justify-center'">
        <div class="w-9 h-9 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-600 dark:text-emerald-400 shrink-0 text-sm">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-150 delay-100"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="overflow-hidden">
            <span class="block text-sm font-semibold text-slate-900 dark:text-white truncate whitespace-nowrap">{{ Auth::user()->name }}</span>
            <span class="block text-xs text-slate-400 truncate mt-0.5 whitespace-nowrap">{{ Auth::user()->role->display_name }}</span>
        </div>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 py-4 space-y-1 overflow-y-auto sidebar-scroll"
        :class="sidebarOpen ? 'px-3' : 'px-2'">

        @php
        $linkBase = "flex items-center rounded-xl text-sm font-medium transition-all duration-200 group relative";
        $linkPad = "gap-3 px-3 py-2.5";
        $linkIcon = "py-2.5 justify-center";
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            :class="[
               '{{ $linkBase }}',
               sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}',
               (activeHash === '#dashboard' || activeHash === '')
                   ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-semibold'
                   : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'
           ]"
            @click="activeHash = '#dashboard'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            {{-- Tooltip saat mini --}}
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">Dashboard</span>
        </a> @if(Auth::user()->isSuperAdmin())
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Manajemen Data</div>
        @foreach([
        ['href' => route('admin.users.index'), 'hash' => '#users-section', 'label' => 'Daftar Pengguna', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['href' => route('admin.kelas.index'), 'hash' => '#kelas', 'label' => 'Manajemen Kelas', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['href' => route('admin.mapel.index'), 'hash' => '#mapel', 'label' => 'Mata Pelajaran', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ] as $item)
        <a href="{{ $item['href'] }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '{{ $item['hash'] }}' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '{{ $item['hash'] }}'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $item['label'] }}</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

        @if(Auth::user()->isWaliKelas())
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Wali Kelas</div>
        @foreach([
        ['href' => route('dashboard').'#kelas-binaan-section', 'hash' => '#kelas-binaan-section', 'label' => 'Kelas Binaan', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['href' => route('wali.siswa.index'), 'hash' => '#wali-siswa', 'label' => 'Siswa Aktif', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['href' => route('wali.absensi.index'), 'hash' => '#wali-absensi', 'label' => 'Rekap Absensi', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ] as $item)
        <a href="{{ $item['href'] }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '{{ $item['hash'] }}' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '{{ $item['hash'] }}'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $item['label'] }}</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

        @if(Auth::user()->isGuru())
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Guru Mapel</div>
        @foreach([
        ['href' => route('guru.jadwal.index'), 'hash' => '#jadwal-mengajar', 'label' => 'Jadwal Mengajar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['href' => route('guru.elearning.index'), 'hash' => '#elearning-section', 'label' => 'E-learning Modul', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['href' => route('guru.nilai.index'), 'hash' => '#nilai-section', 'label' => 'Input Nilai Siswa', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
        ['href' => route('guru.cbt.index'), 'hash' => '#cbt-section', 'label' => 'CBT & Bank Soal', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ] as $item)
        <a href="{{ $item['href'] }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '{{ $item['hash'] }}' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '{{ $item['hash'] }}'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $item['label'] }}</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

        @if(Auth::user()->isGuruBk())
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Konseling & BK</div>
        @foreach([
        ['href' => route('bk.pelanggaran.index'), 'hash' => '#kasus-section', 'label' => 'Catatan Pelanggaran', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['href' => route('bk.pelanggaran.index').'#tambah-kasus-section', 'hash' => '#tambah-kasus-section', 'label' => 'Input Pelanggaran', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
        ] as $item)
        <a href="{{ $item['href'] }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '{{ $item['hash'] }}' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '{{ $item['hash'] }}'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $item['label'] }}</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

        @if(Auth::user()->isSiswa())
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Siswa</div>
        @foreach([
        ['href' => route('siswa.elearning.index'), 'hash' => '#siswa-elearning', 'label' => 'E-learning Modul', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['href' => route('siswa.cbt.index'), 'hash' => '#siswa-cbt', 'label' => 'Ujian CBT Online', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['href' => route('siswa.jadwal.index'), 'hash' => '#siswa-jadwal', 'label' => 'Jadwal Belajar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['href' => route('siswa.presensi.index'), 'hash' => '#siswa-presensi', 'label' => 'Presensi Saya', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ] as $item)
        <a href="{{ $item['href'] }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '{{ $item['hash'] }}' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '{{ $item['hash'] }}'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">{{ $item['label'] }}</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">{{ $item['label'] }}</span>
        </a>
        @endforeach
        @endif

        {{-- Settings --}}
        <div x-show="sidebarOpen" class="pt-3 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengaturan</div>
        <a href="{{ route('profile.edit') }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', activeHash === '#profile' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900']"
            @click="activeHash = '#profile'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap">Profil Saya</span>
            <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">Profil Saya</span>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="border-t border-slate-100 dark:border-slate-800 shrink-0 bg-slate-50/50 dark:bg-slate-900/50"
        :class="sidebarOpen ? 'p-3 flex flex-col gap-1' : 'p-2 flex flex-col items-center gap-1'">

        {{-- Dark Mode --}}
        <button onclick="toggleDarkMode()"
            :title="sidebarOpen ? '' : 'Ubah Mode Warna'"
            :class="sidebarOpen ? 'flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors' : 'p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors'">
            <span :class="sidebarOpen ? 'flex items-center gap-2' : ''">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Ubah Mode</span>
            </span>
        </button>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                :title="sidebarOpen ? '' : 'Keluar'"
                :class="sidebarOpen ? 'flex items-center gap-2 w-full px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors' : 'flex justify-center w-full p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Keluar</span>
            </button>
        </form>
    </div>
</div>