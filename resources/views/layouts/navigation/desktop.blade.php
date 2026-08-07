{{-- Sidebar partial — sidebarOpen from body x-data --}}
<div class="flex flex-col h-full bg-white dark:bg-slate-900">

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
        @if(Auth::user()->photo_profile)
            <img src="{{ asset('storage/' . Auth::user()->photo_profile) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
        @else
            <div class="w-9 h-9 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-600 dark:text-emerald-400 shrink-0 text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        @endif
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
    <nav class="flex-1 py-4 space-y-3 overflow-y-auto sidebar-scroll"
        :class="sidebarOpen ? 'px-3' : 'px-2'">

        @php
        $linkBase = "flex items-center rounded-xl text-sm font-medium transition-all duration-200 group relative";
        $linkPad = "gap-3 px-3 py-2.5";
        $linkIcon = "py-2.5 justify-center"        @endphp

        @if(!Auth::user()->isSuperAdmin())
            <a href="{{ route('dashboard') }}"
                :class="[
                   '{{ $linkBase }}',
                   sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}',
                   '{{ request()->routeIs('dashboard') ? 'active-stabilo' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}'
                 ]">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                <span x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">Dashboard</span>
            </a>
        @endif

        @if(Auth::user()->isAdmin())
            {{-- Group: Profil Madrasah --}}
            <div x-show="sidebarOpen" class="space-y-1">
                <a href="{{ route('admin.profil.index') }}" class="flex items-center gap-3 w-full rounded-xl text-xs font-semibold transition-all duration-200 px-3 py-2.5 {{ request()->routeIs('admin.profil.*') ? 'active-stabilo' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Profil Madrasah</span>
                </a>
            </div>

            {{-- Group: Pengaturan --}}
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('admin.tahun_ajaran.*', 'admin.holidays.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('admin.tahun_ajaran.*', 'admin.holidays.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <span>Pengaturan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('admin.tahun_ajaran.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.tahun_ajaran.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Tahun Ajaran & Semester</a>
                    <a href="{{ route('admin.holidays.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.holidays.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Kalender Libur</a>
                </div>
            </div>

            {{-- Group: Master Data --}}
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('admin.mapel.*', 'admin.kelas.*', 'admin.siswa.*', 'admin.guru.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('admin.mapel.*', 'admin.kelas.*', 'admin.siswa.*', 'admin.guru.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        <span>Master Data</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('admin.guru.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.guru.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Data Guru</a>
                    <div class="px-3 py-1 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-t border-slate-50 dark:border-slate-800 mt-1">Tsanawiyah (MTs)</div>
                    <a href="{{ route('admin.mapel.index', ['unit' => 'MTs']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.mapel.*') && request('unit', 'MTs') === 'MTs') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Mata Pelajaran</a>
                    <a href="{{ route('admin.kelas.index', ['unit' => 'MTs']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.kelas.*') && request('unit', 'MTs') === 'MTs') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-850' }}">Data Kelas</a>
                    <a href="{{ route('admin.siswa.index', ['unit' => 'MTs']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.siswa.*') && request('unit', 'MTs') === 'MTs') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Data Siswa</a>
                    <div class="px-3 py-1 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-t border-slate-50 dark:border-slate-800 mt-1">Aliyah (MA)</div>
                    <a href="{{ route('admin.mapel.index', ['unit' => 'MA']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.mapel.*') && request('unit') === 'MA') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Mata Pelajaran</a>
                    <a href="{{ route('admin.kelas.index', ['unit' => 'MA']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.kelas.*') && request('unit') === 'MA') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-855 hover:text-slate-855' }}">Data Kelas</a>
                    <a href="{{ route('admin.siswa.index', ['unit' => 'MA']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.siswa.*') && request('unit') === 'MA') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Data Siswa</a>
                </div>
            </div>

            {{-- Group: Kesiswaan --}}
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('admin.pindah_kelas.*', 'admin.naik_kelas.*', 'admin.alumni.*', 'admin.absensi.*', 'bk.triase.*', 'bk.pelanggaran.*', 'admin.ppdb.*') || request()->routeIs('admin.siswa.history') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('admin.pindah_kelas.*', 'admin.naik_kelas.*', 'admin.alumni.*', 'admin.absensi.*', 'bk.triase.*', 'bk.pelanggaran.*', 'admin.ppdb.*') || request()->routeIs('admin.siswa.history') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kesiswaan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('admin.ppdb.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.ppdb.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">PPDB (Penerimaan Siswa Baru)</a>
                    <a href="{{ route('admin.pindah_kelas.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.pindah_kelas.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Pindah Kelas</a>
                    <a href="{{ route('admin.naik_kelas.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.naik_kelas.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Naik Kelas Massal</a>
                    <a href="{{ route('admin.alumni.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.alumni.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Direktori Alumni</a>
                    <a href="{{ route('admin.absensi.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.absensi.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Manajemen Absensi</a>
                    <a href="{{ route('bk.triase.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('bk.triase.*', 'bk.pelanggaran.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">BK & Konseling</a>
                </div>
            </div>

            {{-- Group: Website & Publikasi --}}
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('admin.publikasi.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('admin.publikasi.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span>Website & Publikasi</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Umum']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.berita.*') && request('category', 'Umum') === 'Umum') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Berita</a>
                    <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Akademik']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.berita.*') && request('category') === 'Akademik') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Pengumuman</a>
                    <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Agenda']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.berita.*') && request('category') === 'Agenda') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Agenda</a>
                    <a href="{{ route('admin.publikasi.galeri.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.galeri.*') && !request('sub')) ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Galeri</a>
                    <a href="{{ route('admin.publikasi.galeri.index', ['sub' => 'video']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.galeri.*') && request('sub') === 'video') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Video</a>
                    <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Dokumen']) }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ (request()->routeIs('admin.publikasi.berita.*') && request('category') === 'Dokumen') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Dokumen</a>
                    <a href="{{ route('admin.publikasi.banner.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.publikasi.banner.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Banner</a>
                    <a href="{{ route('admin.publikasi.medsos.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.publikasi.medsos.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Media Sosial</a>
                </div>
            </div>

            {{-- Group: Sistem --}}
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.sistem.*', 'admin.integrasi.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('admin.users.*', 'admin.sistem.*', 'admin.integrasi.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <span>Sistem</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.users.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Pengguna & Hak Akses</a>
                    <a href="{{ route('admin.sistem.logs') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.sistem.logs') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Log Aktivitas</a>
                    <a href="{{ route('admin.sistem.audit') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.sistem.audit') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Laporan Audit</a>
                    <a href="{{ route('admin.sistem.backup') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.sistem.backup') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Backup & Pemulihan</a>
                    <a href="{{ route('admin.integrasi.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('admin.integrasi.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Integrasi Database MTs</a>
                </div>
            </div>
        @endif



        {{-- Group: Wali Kelas --}}
        @if(Auth::user()->isWaliKelas())
            <div x-show="sidebarOpen" x-data="{ open: {{ (request()->routeIs('wali.*') || request()->routeIs('absensi.officer.*')) ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ (request()->routeIs('wali.*') || request()->routeIs('absensi.officer.*')) ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-650 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Wali Kelas</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('wali.dashboard') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('wali.dashboard') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Dashboard Kelas</a>
                    <a href="{{ route('wali.siswa.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('wali.siswa.index') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Siswa Binaan</a>
                    <a href="{{ route('absensi.officer.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('absensi.officer.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Input Absensi Harian</a>
                    <a href="{{ route('wali.absensi.verifikasi.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('wali.absensi.verifikasi.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Verifikasi Absensi</a>
                    <a href="{{ route('wali.officer.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('wali.officer.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">
                        <span class="flex items-center gap-1.5">Petugas Absensi <span class="px-1 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded text-[9px] font-bold">Atur</span></span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Group: Guru Mapel --}}
        @if(Auth::user()->isGuru())
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('guru.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('guru.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Guru Mapel</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('guru.jadwal.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('guru.jadwal.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Jadwal Mengajar</a>
                    <a href="{{ route('guru.elearning.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('guru.elearning.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">E-learning Modul</a>
                    <a href="{{ route('guru.nilai.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('guru.nilai.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Input Nilai Siswa</a>
                    <a href="{{ route('guru.cbt.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('guru.cbt.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">CBT & Bank Soal</a>
                    <a href="{{ route('guru.tahfidz.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('guru.tahfidz.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Setoran Tahfidz</a>
                </div>
            </div>
        @endif

        {{-- Group: Guru BK --}}
        @if(Auth::user()->isGuruBk())
            <div x-show="sidebarOpen" x-data="{ open: {{ request()->routeIs('bk.triase.*', 'bk.pelanggaran.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="flex items-center justify-between w-full rounded-xl text-xs font-semibold transition-all duration-200 gap-3 px-3 py-2.5 {{ request()->routeIs('bk.triase.*', 'bk.pelanggaran.*') ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-450 border border-emerald-100/50 dark:border-emerald-900/30' : 'text-slate-655 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Konseling & BK</span>
                    </div>
                    <svg class="w-3.5 h-3.5 transform transition-transform duration-205" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-7 pr-1 space-y-1">
                    <a href="{{ route('bk.triase.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('bk.triase.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Antrean & Triase</a>
                    <a href="{{ route('bk.pelanggaran.index') }}" class="block px-3 py-2 rounded-lg text-[11px] font-semibold {{ request()->routeIs('bk.pelanggaran.*') ? 'active-stabilo' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-800' }}">Catatan Pelanggaran</a>
                </div>
            </div>
        @endif

        {{-- Group: Petugas Absensi (siswa yang ditugaskan, ditampilkan MENGGANTIKAN menu absensi biasa) --}}
        @php
        $assignedClass = \App\Models\AttendanceOfficerAssignment::where('user_id', Auth::user()->id)->where('active', true)->first();
        @endphp
        @if($assignedClass)
            <div x-show="sidebarOpen" class="space-y-1.5">
                <span class="block px-3 text-[9px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mt-1">Input Absensi</span>
                <a href="{{ route('absensi.officer.index', $assignedClass->class_id) }}" class="flex items-center rounded-xl text-sm font-medium transition-all duration-200 group relative gap-3 px-3 py-2.5 {{ request()->routeIs('absensi.officer.*') ? 'active-stabilo' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="whitespace-nowrap">Input Absensi Kelas</span>
                </a>
            </div>
        @endif

        {{-- Group: Siswa (tidak ada lagi menu khusus role siswa) --}}
        {{-- Siswa yang ditugaskan sebagai petugas absensi sudah ditampilkan di atas --}}

        {{-- Miniature Icons for collapsed sidebar mode --}}
        <div x-show="!sidebarOpen" class="space-y-3 pt-2">
            @if(Auth::user()->isAdmin())
                {{-- Group: Profil Madrasah --}}
                <div class="group relative flex justify-center py-1">
                    <a href="{{ route('admin.profil.index') }}" class="p-2 border rounded-xl block {{ request()->routeIs('admin.profil.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        🏫
                    </a>
                    <span class="absolute left-full ml-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">Profil Madrasah</span>
                </div>

                {{-- Group: Pengaturan --}}
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('admin.tahun_ajaran.*', 'admin.holidays.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">⚙️</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Pengaturan</span>
                        <a href="{{ route('admin.tahun_ajaran.index') }}" class="block hover:text-emerald-300">Tahun Ajaran & Semester</a>
                        <a href="{{ route('admin.holidays.index') }}" class="block hover:text-emerald-300">Kalender Libur</a>
                    </div>
                </div>

                {{-- Group: Master Data --}}
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('admin.mapel.*', 'admin.kelas.*', 'admin.siswa.*', 'admin.guru.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">📁</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Master Data</span>
                        <a href="{{ route('admin.guru.index') }}" class="block hover:text-emerald-300">Data Guru</a>
                        <span class="font-bold text-[9px] uppercase text-slate-400 block pt-1 border-t border-slate-800">Tsanawiyah (MTs)</span>
                        <a href="{{ route('admin.mapel.index', ['unit' => 'MTs']) }}" class="block hover:text-emerald-300 pl-2">Mapel MTs</a>
                        <a href="{{ route('admin.kelas.index', ['unit' => 'MTs']) }}" class="block hover:text-emerald-300 pl-2">Kelas MTs</a>
                        <a href="{{ route('admin.siswa.index', ['unit' => 'MTs']) }}" class="block hover:text-emerald-300 pl-2">Siswa MTs</a>
                        <span class="font-bold text-[9px] uppercase text-slate-400 block pt-1 border-t border-slate-800">Aliyah (MA)</span>
                        <a href="{{ route('admin.mapel.index', ['unit' => 'MA']) }}" class="block hover:text-emerald-300 pl-2">Mapel MA</a>
                        <a href="{{ route('admin.kelas.index', ['unit' => 'MA']) }}" class="block hover:text-emerald-300 pl-2">Kelas MA</a>
                        <a href="{{ route('admin.siswa.index', ['unit' => 'MA']) }}" class="block hover:text-emerald-300 pl-2">Siswa MA</a>
                    </div>
                </div>

                {{-- Group: Kesiswaan --}}
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('admin.pindah_kelas.*', 'admin.naik_kelas.*', 'admin.alumni.*', 'admin.absensi.*', 'bk.triase.*', 'bk.pelanggaran.*', 'admin.ppdb.*') || request()->routeIs('admin.siswa.history') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">🎓</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Kesiswaan</span>
                        <a href="{{ route('admin.ppdb.index') }}" class="block hover:text-emerald-300">PPDB Baru</a>
                        <a href="{{ route('admin.pindah_kelas.index') }}" class="block hover:text-emerald-300">Pindah Kelas</a>
                        <a href="{{ route('admin.naik_kelas.index') }}" class="block hover:text-emerald-300">Naik Kelas Massal</a>
                        <a href="{{ route('admin.alumni.index') }}" class="block hover:text-emerald-300">Direktori Alumni</a>
                        <a href="{{ route('admin.absensi.index') }}" class="block hover:text-emerald-300">Manajemen Absensi</a>
                        <a href="{{ route('bk.triase.index') }}" class="block hover:text-emerald-300">BK & Konseling</a>
                    </div>
                </div>

                {{-- Group: Website & Publikasi --}}
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('admin.publikasi.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">📢</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Website & Publikasi</span>
                        <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Umum']) }}" class="block hover:text-emerald-300">Berita</a>
                        <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Akademik']) }}" class="block hover:text-emerald-300">Pengumuman</a>
                        <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Agenda']) }}" class="block hover:text-emerald-300">Agenda</a>
                        <a href="{{ route('admin.publikasi.galeri.index') }}" class="block hover:text-emerald-300">Galeri</a>
                        <a href="{{ route('admin.publikasi.galeri.index', ['sub' => 'video']) }}" class="block hover:text-emerald-300">Video</a>
                        <a href="{{ route('admin.publikasi.berita.index', ['category' => 'Dokumen']) }}" class="block hover:text-emerald-300">Dokumen</a>
                        <a href="{{ route('admin.publikasi.banner.index') }}" class="block hover:text-emerald-300">Banner</a>
                        <a href="{{ route('admin.publikasi.medsos.index') }}" class="block hover:text-emerald-300">Media Sosial</a>
                    </div>
                </div>

                {{-- Group: Sistem --}}
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('admin.users.*', 'admin.sistem.*', 'admin.integrasi.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">🔧</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Sistem</span>
                        <a href="{{ route('admin.users.index') }}" class="block hover:text-emerald-300">Pengguna & Hak Akses</a>
                        <a href="{{ route('admin.sistem.logs') }}" class="block hover:text-emerald-300">Log Aktivitas</a>
                        <a href="{{ route('admin.sistem.audit') }}" class="block hover:text-emerald-300">Laporan Audit</a>
                        <a href="{{ route('admin.sistem.backup') }}" class="block hover:text-emerald-300">Backup & Pemulihan</a>
                        <a href="{{ route('admin.integrasi.index') }}" class="block hover:text-emerald-300">Integrasi Database MTs</a>
                    </div>
                </div>
            @endif



            {{-- Wali Kelas collapsed mode --}}
            @if(Auth::user()->isWaliKelas())
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ (request()->routeIs('wali.*') || request()->routeIs('absensi.officer.*')) ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">🏫</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Wali Kelas</span>
                        <a href="{{ route('wali.dashboard') }}" class="block hover:text-emerald-300">Dashboard Kelas</a>
                        <a href="{{ route('wali.siswa.index') }}" class="block hover:text-emerald-300">Siswa Binaan</a>
                        <a href="{{ route('absensi.officer.index') }}" class="block hover:text-emerald-300">Input Absensi Harian</a>
                        <a href="{{ route('wali.absensi.verifikasi.index') }}" class="block hover:text-emerald-300">Verifikasi Absensi</a>
                        <a href="{{ route('wali.officer.index') }}" class="block hover:text-emerald-300">Petugas Absensi</a>
                    </div>
                </div>
            @endif

            {{-- Guru Mapel collapsed mode --}}
            @if(Auth::user()->isGuru())
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('guru.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">📚</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Guru Mapel</span>
                        <a href="{{ route('guru.jadwal.index') }}" class="block hover:text-emerald-300">Jadwal Mengajar</a>
                        <a href="{{ route('guru.elearning.index') }}" class="block hover:text-emerald-300">E-learning Modul</a>
                        <a href="{{ route('guru.nilai.index') }}" class="block hover:text-emerald-300">Input Nilai Siswa</a>
                        <a href="{{ route('guru.cbt.index') }}" class="block hover:text-emerald-300">CBT & Bank Soal</a>
                        <a href="{{ route('guru.tahfidz.index') }}" class="block hover:text-emerald-300">Setoran Tahfidz</a>
                    </div>
                </div>
            @endif

            {{-- Guru BK collapsed mode --}}
            @if(Auth::user()->isGuruBk())
                <div class="group relative flex justify-center py-1">
                    <span class="p-2 border rounded-xl block cursor-default {{ request()->routeIs('bk.triase.*', 'bk.pelanggaran.*') ? 'active-stabilo' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-660 dark:text-slate-400' }}">🧠</span>
                    <div class="absolute left-full ml-2 py-2 px-3 bg-slate-900 text-white text-xs rounded-xl shadow-xl hidden group-hover:block whitespace-nowrap z-50 space-y-1">
                        <span class="font-bold text-[10px] uppercase text-emerald-400 block border-b border-slate-800 pb-1 mb-1">Konseling & BK</span>
                        <a href="{{ route('bk.triase.index') }}" class="block hover:text-emerald-300">Antrean & Triase</a>
                        <a href="{{ route('bk.pelanggaran.index') }}" class="block hover:text-emerald-300">Catatan Pelanggaran</a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Settings (Always visible at bottom) --}}
        <a href="{{ route('profile.edit') }}"
            :class="['{{ $linkBase }}', sidebarOpen ? '{{ $linkPad }}' : '{{ $linkIcon }}', '{{ request()->routeIs('profile.edit') ? 'active-stabilo' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900' }}']">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap text-xs">Profil Saya</span>
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