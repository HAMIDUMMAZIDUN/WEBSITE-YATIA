<!DOCTYPE html>
<html lang="id" class="{{ (isset($school_profile) && $school_profile->theme_mode === 'dark') ? 'dark' : '' }}" x-data="{ darkMode: {{ (isset($school_profile) && $school_profile->theme_mode === 'dark') ? 'true' : 'false' }} }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Manajemen Sekolah (SIMS) Terintegrasi YATIA - Portal Resmi Madrasah MTs & MA YATIA.">
    <title>Madrasah MTs & MA YATIA - Portal Sekolah Terintegrasi</title>

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

        h1, h2, h3, h4, .font-display {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased min-h-screen flex flex-col transition-colors duration-300">

    <!-- Fallback & Mock Data Setup -->
    @php
        if (!function_exists('parseEmbedLink')) {
            function parseEmbedLink($url) {
                if (!$url) return null;
                
                // YouTube
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches)) {
                    return [
                        'type' => 'youtube',
                        'id' => $matches[1],
                        'embed_url' => 'https://www.youtube.com/embed/' . $matches[1]
                    ];
                }
                
                // Instagram
                if (preg_match('/instagram\.com\/(?:p|reel|tv)\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    return [
                        'type' => 'instagram',
                        'id' => $matches[1],
                        'embed_url' => 'https://www.instagram.com/p/' . $matches[1] . '/embed'
                    ];
                }

                // TikTok
                if (preg_match('/tiktok\.com\/@[^\/]+\/video\/(\d+)/', $url, $matches)) {
                    return [
                        'type' => 'tiktok',
                        'id' => $matches[1],
                        'embed_url' => 'https://www.tiktok.com/embed/v2/' . $matches[1]
                    ];
                }
                
                return [
                    'type' => 'generic',
                    'id' => null,
                    'embed_url' => $url
                ];
            }
        }

        $school_profile = $profile ?? \App\Models\SchoolProfile::firstOrCreate(['id' => 1]);
        $banners = $banners ?? collect();
        $articles = $articles ?? collect();
        $galleries = $galleries ?? collect();
        $socials = $socials ?? collect();

        $firstBanner = $banners->first();
        $heroTitle = $firstBanner ? $firstBanner->title : 'Membentuk Generasi Islami, Cerdas, dan Berprestasi';
        $heroSubtitle = $firstBanner ? $firstBanner->subtitle : 'Pilihan terbaik untuk masa depan putra-putri Anda yang berlandaskan iman, ilmu, dan takwa.';
        $heroBg = ($firstBanner && $firstBanner->image_path) ? $firstBanner->image_path : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=1600';
        $heroLink = ($firstBanner && $firstBanner->link_url) ? $firstBanner->link_url : '#program';

        // Keunggulan / Program Prioritas
        $keunggulan_decoded = json_decode($school_profile->keunggulan_json, true);
        if ($keunggulan_decoded && is_array($keunggulan_decoded) && count($keunggulan_decoded) === 4) {
            $keunggulan = [
                [
                    'title' => $keunggulan_decoded[0]['title'] ?: 'Program Tahfidz Qur\'an',
                    'desc' => $keunggulan_decoded[0]['desc'] ?: 'Target hafalan terstruktur dibimbing oleh asatidzah berkompeten untuk mencetak hafizh/hafizhah.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                ],
                [
                    'title' => $keunggulan_decoded[1]['title'] ?: 'Fasilitas Lengkap',
                    'desc' => $keunggulan_decoded[1]['desc'] ?: 'Ruang kelas ber-AC, laboratorium komputer modern, perpustakaan digital, serta sarana olahraga.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>'
                ],
                [
                    'title' => $keunggulan_decoded[2]['title'] ?: 'Ekstrakurikuler Berprestasi',
                    'desc' => $keunggulan_decoded[2]['desc'] ?: 'Mengembangkan bakat siswa melalui Pramuka, Robotik, Seni Kaligrafi, Hadrah, Futsal, dan OSN.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>'
                ],
                [
                    'title' => $keunggulan_decoded[3]['title'] ?: 'Kurikulum Terpadu',
                    'desc' => $keunggulan_decoded[3]['desc'] ?: 'Integrasi antara Kurikulum Nasional (Merdeka) dengan nilai-nilai kepesantrenan dan akhlakul karimah.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
                ]
            ];
        } else {
            $keunggulan = [
                [
                    'title' => 'Program Tahfidz Qur\'an',
                    'desc' => 'Target hafalan terstruktur dibimbing oleh asatidzah berkompeten untuk mencetak hafizh/hafizhah.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                ],
                [
                    'title' => 'Fasilitas Lengkap',
                    'desc' => 'Ruang kelas ber-AC, laboratorium komputer modern, perpustakaan digital, serta sarana olahraga.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>'
                ],
                [
                    'title' => 'Ekstrakurikuler Berprestasi',
                    'desc' => 'Mengembangkan bakat siswa melalui Pramuka, Robotik, Seni Kaligrafi, Hadrah, Futsal, dan OSN.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>'
                ],
                [
                    'title' => 'Kurikulum Terpadu',
                    'desc' => 'Integrasi antara Kurikulum Nasional (Merdeka) dengan nilai-nilai kepesantrenan dan akhlakul karimah.',
                    'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
                ]
            ];
        }

        // Dummy/Fallback Berita jika kosong
        $dummyArticles = [
            (object)[
                'title' => 'Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran 2026/2027 Resmi Dibuka',
                'category' => 'Akademik',
                'published_at' => now(),
                'image_path' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800',
                'content' => 'Madrasah YATIA resmi membuka pendaftaran peserta didik baru untuk jenjang MTs dan MA. Daftarkan segera putra-putri Anda secara online.'
            ],
            (object)[
                'title' => 'Siswa MA YATIA Menyabet Juara Umum Olimpiade Sains Nasional Tingkat Provinsi',
                'category' => 'Prestasi',
                'published_at' => now()->subDays(2),
                'image_path' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800',
                'content' => 'Prestasi gemilang diraih siswa-siswi kita dalam OSN tingkat provinsi cabang Fisika, Kimia, dan Astronomi.'
            ],
            (object)[
                'title' => 'Pelaksanaan Rutin Program Khotmil Qur\'an Mingguan & Doa Bersama',
                'category' => 'Agenda',
                'published_at' => now()->subDays(5),
                'image_path' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&q=80&w=800',
                'content' => 'Kegiatan rutin Khotmil Qur\'an mingguan diikuti oleh seluruh guru, staf, dan siswa guna menanamkan kecintaan pada kalamullah.'
            ]
        ];

        // Dummy/Fallback Galeri jika kosong
        $dummyGalleries = [
            (object)['title' => 'Upacara Peringatan Hari Santri', 'file_path' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
            (object)['title' => 'Praktikum Laboratorium Komputer', 'file_path' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
            (object)['title' => 'Penyimakan Setoran Tahfidz Qur\'an', 'file_path' => 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
            (object)['title' => 'Kegiatan Latihan Pramuka', 'file_path' => 'https://images.unsplash.com/photo-1526256262350-7da7584cf5eb?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
            (object)['title' => 'Masjid Kampus Al-Ikhlas YATIA', 'file_path' => 'https://images.unsplash.com/photo-1590075865003-e48277afd558?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
            (object)['title' => 'Pentas Seni & Kreasi Islami', 'file_path' => 'https://images.unsplash.com/photo-1492534513006-37715f336a39?auto=format&fit=crop&q=80&w=600', 'type' => 'photo'],
        ];

        $displayArticles = $articles->isEmpty() ? collect($dummyArticles) : $articles;
        $displayGalleries = $galleries->isEmpty() ? collect($dummyGalleries) : $galleries;
    @endphp

    <!-- ================= NAVBAR SECTION ================= -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-white/90 dark:bg-slate-950/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 dark:border-slate-900 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 text-emerald-600 dark:text-emerald-450 shrink-0">
                        <x-application-logo class="w-full h-full" />
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white font-display">MADRASAH YATIA</span>
                        <span class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-bold tracking-wider uppercase">Portal Akademik Terpadu</span>
                    </div>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden lg:flex items-center space-x-6 h-full">
                    <a href="#beranda" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Beranda</a>
                    <a href="#profil" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Profil</a>
                    <a href="#program" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Akademik</a>
                    <a href="#berita" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Berita</a>
                    <a href="#galeri" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Galeri</a>
                    <a href="#kontak" class="nav-link inline-flex items-center h-full border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-600 dark:hover:border-emerald-400 transition-all duration-200">Kontak</a>
                </div>

                <!-- Action Buttons & Theme Toggle -->
                <div class="hidden lg:flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" /></svg>
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>

                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-xl text-sm font-semibold border-2 border-emerald-600 dark:border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-900 transition-colors">
                        Login Portal
                    </a>
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/10 hover:shadow-lg transition-all hover:-translate-y-0.5">
                        Daftar PPDB
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden gap-3">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" /></svg>
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Dropdown -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="lg:hidden bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 px-4 py-4 space-y-3">
            <a href="#beranda" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Beranda</a>
            <a href="#profil" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Profil</a>
            <a href="#program" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Akademik</a>
            <a href="#berita" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Berita</a>
            <a href="#galeri" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Galeri</a>
            <a href="#kontak" @click="mobileMenuOpen = false" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 py-2">Kontak</a>
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="text-center px-4 py-2.5 rounded-xl border border-emerald-600 dark:border-emerald-500 text-emerald-600 dark:text-emerald-400 text-sm font-semibold">Login Portal</a>
                <a href="{{ route('login') }}" class="text-center px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold">Daftar PPDB</a>
            </div>
        </div>
    </nav>


    <!-- ================= HERO SECTION ================= -->
    <section id="beranda" class="relative overflow-hidden bg-cover bg-center bg-no-repeat py-32 lg:py-48" style="background-image: url('{{ $heroBg }}');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-slate-950/70 dark:bg-slate-950/80 backdrop-blur-[2px]"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-8">
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold tracking-tight text-white leading-tight font-display">
                {!! $heroTitle !!}
            </h1>
            <p class="text-lg sm:text-xl text-slate-200 max-w-3xl mx-auto leading-relaxed">
                {{ $heroSubtitle }}
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-4">
                <a href="#program" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-base font-bold shadow-lg shadow-emerald-600/20 transition-all hover:-translate-y-0.5">
                    Jelajahi Program Kami
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 rounded-xl text-base font-bold border border-white/20 text-white transition-colors">
                    Daftar Calon Siswa
                </a>
            </div>
        </div>
    </section>


    <!-- ================= WIDGET KHAS MADRASAH ================= -->
    <section class="relative z-20 -mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- Jadwal Shalat Widget -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl text-emerald-600 dark:text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" /></svg>
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">Jadwal Shalat Hari Ini</h3>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold font-display">Wilayah {{ $school_profile->jadwal_shalat_kota ?: 'DKI Jakarta & Sekitarnya' }}</p>
                        </div>
                    </div>

                    <!-- Horizontal Grid Shalat -->
                    <div class="grid grid-cols-5 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl text-center border border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider">Subuh</span>
                            <span class="block text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">04:45</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl text-center border border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider">Dzuhur</span>
                            <span class="block text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">12:02</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl text-center border border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider">Ashar</span>
                            <span class="block text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">15:22</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl text-center border border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider">Maghrib</span>
                            <span class="block text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">17:58</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-850 p-3 rounded-2xl text-center border border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-455 uppercase tracking-wider">Isya</span>
                            <span class="block text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">19:10</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 border-t border-slate-100 dark:border-slate-850 pt-2 flex justify-between items-center text-[10px] text-slate-400">
                    <span>*Jadwal dapat bervariasi beberapa menit</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Kemenag RI</span>
                </div>
            </div>

            <!-- Kutipan Hari Ini Widget -->
            <div class="bg-emerald-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between">
                <!-- Background decoration -->
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-800 rounded-full opacity-30 pointer-events-none"></div>

                <div class="relative z-10">
                    <span class="text-xs uppercase tracking-wider font-bold text-emerald-300 font-display">Kutipan Hari Ini</span>
                    <blockquote class="mt-3 text-base italic font-medium leading-relaxed text-slate-100">
                        "{{ $school_profile->kutipan_teks ?: 'Allah akan meninggikan orang-orang yang beriman di antaramu dan orang-orang yang diberi ilmu pengetahuan beberapa derajat.' }}"
                    </blockquote>
                </div>
                <div class="mt-4 border-t border-emerald-800 pt-2 flex justify-between items-center text-xs text-emerald-300 relative z-10">
                    <span>{{ $school_profile->kutipan_sumber ?: 'QS. Al-Mujadilah: 11' }}</span>
                    <span class="font-bold text-yellow-400">★ Ilmu & Iman</span>
                </div>
            </div>
            
        </div>
    </section>


    <!-- ================= PROFIL SECTION ================= -->
    <section id="profil" class="py-24 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1544717302-d1e8e5248553?auto=format&fit=crop&q=80&w=800" class="rounded-3xl shadow-xl border border-slate-100 dark:border-slate-800" alt="Siswa Madrasah YATIA">
                    <div class="absolute -bottom-6 -right-6 bg-white dark:bg-slate-900 border border-slate-150 dark:border-slate-800 p-6 rounded-2xl shadow-lg hidden md:block">
                        <span class="block text-2xl font-bold text-emerald-600 dark:text-emerald-400">Akreditasi A</span>
                        <span class="text-xs text-slate-400">Kualitas Pendidikan Unggul Terjamin</span>
                    </div>
                </div>
                <div class="space-y-6">
                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">Sekilas Profil</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 dark:text-white font-display leading-tight">Mewujudkan Insan Kaffah Berwawasan Global</h2>
                    <p class="text-slate-655 dark:text-slate-400 leading-relaxed">
                        {{ $school_profile->deskripsi_singkat ?: 'Madrasah Tsanawiyah (MTs) & Madrasah Aliyah (MA) YATIA merupakan lembaga pendidikan Islam modern yang berkomitmen memadukan kurikulum akademik bermutu dengan pembentukan moral berbasis nilai kepesantrenan. Kami membimbing siswa untuk berprestasi secara rasional namun tetap teguh memegang akhlak islami.' }}
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <span class="block font-bold text-slate-800 dark:text-white">Visi</span>
                            <span class="text-xs text-slate-400 block mt-1">
                                {{ $school_profile->visi ?: 'Unggul dalam prestasi akademik, berakhlak mulia, dan kompetitif secara global.' }}
                            </span>
                        </div>
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <span class="block font-bold text-slate-800 dark:text-white">Misi</span>
                            <div class="text-xs text-slate-400 block mt-1 space-y-1">
                                @if($school_profile->misi)
                                    @foreach(explode("\n", str_replace("\r", "", $school_profile->misi)) as $misi_point)
                                        @if(trim($misi_point))
                                            <div class="flex items-start gap-1">
                                                <span class="text-emerald-500">•</span>
                                                <span>{{ trim($misi_point) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <span>Menyelenggarakan pembelajaran terpadu, bimbingan Tahfidz intensif, dan ekstrakurikuler unggulan.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ================= KEUNGGULAN & PROGRAM PRIORITAS ================= -->
    <section id="program" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <div class="text-center space-y-4">
                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">Keunggulan</span>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white font-display">Mengapa Memilih Kami?</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto text-sm">Fasilitas dan program akademik terpadu yang dirancang khusus untuk kenyamanan belajar serta tumbuh kembang potensi maksimal siswa.</p>
            </div>

            <!-- Loop Keunggulan -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($keunggulan as $item)
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 p-6 rounded-3xl hover:shadow-lg transition-all duration-300 group hover:-translate-y-1">
                        <div class="w-12 h-12 mb-6 rounded-2xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center transition-all group-hover:scale-110">
                            {!! $item['icon'] !!}
                        </div>
                        <h3 class="font-bold text-slate-800 dark:text-white mb-3 text-lg leading-tight">{{ $item['title'] }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ================= COUNTER STATISTIK ================= -->
    <section class="bg-emerald-900 text-white py-16 relative overflow-hidden">
        <!-- Decorative pattern -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-800 via-transparent to-transparent opacity-50"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="space-y-2">
                    <span class="block text-4xl sm:text-5xl font-extrabold font-display text-yellow-400">{{ $school_profile->counter_siswa ?: '850+' }}</span>
                    <span class="text-xs font-bold text-emerald-250 uppercase tracking-widest block">Total Siswa Aktif</span>
                </div>
                <div class="space-y-2">
                    <span class="block text-4xl sm:text-5xl font-extrabold font-display text-yellow-400">{{ $school_profile->counter_guru ?: '45+' }}</span>
                    <span class="text-xs font-bold text-emerald-250 uppercase tracking-widest block">Guru & Staf</span>
                </div>
                <div class="space-y-2">
                    <span class="block text-4xl sm:text-5xl font-extrabold font-display text-yellow-400">{{ $school_profile->counter_ekskul ?: '18+' }}</span>
                    <span class="text-xs font-bold text-emerald-250 uppercase tracking-widest block">Kegiatan Ekskul</span>
                </div>
                <div class="space-y-2">
                    <span class="block text-4xl sm:text-5xl font-extrabold font-display text-yellow-400">{{ $school_profile->counter_penghargaan ?: '35+' }}</span>
                    <span class="text-xs font-bold text-emerald-250 uppercase tracking-widest block">Penghargaan Diraih</span>
                </div>
            </div>
        </div>
    </section>


    <!-- ================= BERITA SECTION ================= -->
    <section id="berita" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <div class="flex justify-between items-end border-b border-slate-100 dark:border-slate-800 pb-6">
                <div class="space-y-3">
                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">Kabar Madrasah</span>
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white font-display">Berita & Agenda Terbaru</h2>
                </div>
                <a href="#" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline hidden md:inline-flex items-center gap-1 text-sm transition-all hover:translate-x-1">
                    Lihat Semua Berita
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <!-- Loop Berita Dinamis -->
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($displayArticles as $article)
                    @php
                        $badgeColor = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                        if ($article->category === 'Akademik') {
                            $badgeColor = 'bg-emerald-600 text-white';
                        } elseif ($article->category === 'Prestasi') {
                            $badgeColor = 'bg-amber-600 text-white';
                        } elseif ($article->category === 'Agenda') {
                            $badgeColor = 'bg-blue-600 text-white';
                        }
                        $embed = !empty($article->embed_link) ? parseEmbedLink($article->embed_link) : null;
                    @endphp
                    <article class="bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-3xl overflow-hidden group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between">
                        <div>
                            <div class="h-52 bg-slate-200 relative overflow-hidden">
                                @if($embed)
                                    @if($embed['type'] === 'youtube')
                                        <iframe src="{{ $embed['embed_url'] }}" class="absolute inset-0 w-full h-full z-10" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    @elseif($embed['type'] === 'instagram')
                                        <iframe src="{{ $embed['embed_url'] }}" class="absolute inset-0 w-full h-full z-10" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
                                    @elseif($embed['type'] === 'tiktok')
                                        <iframe src="{{ $embed['embed_url'] }}" class="absolute inset-0 w-full h-full z-10" frameborder="0" allowfullscreen></iframe>
                                    @else
                                        @if($article->image_path)
                                            <img src="{{ $article->image_path }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $article->title }}">
                                        @endif
                                        <div class="absolute inset-0 bg-slate-950/75 flex flex-col justify-center items-center p-4 text-center z-10">
                                            <svg class="w-8 h-8 text-emerald-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                            <a href="{{ $article->embed_link }}" target="_blank" class="text-[10px] font-bold text-emerald-300 hover:text-emerald-100 break-all underline line-clamp-2 px-2">{{ $article->embed_link }}</a>
                                        </div>
                                    @endif
                                @else
                                    @if($article->image_path)
                                        <img src="{{ $article->image_path }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $article->title }}">
                                    @else
                                        <div class="absolute inset-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                @endif
                                <span class="absolute top-4 left-4 z-20 px-3 py-1 rounded-lg {{ $badgeColor }} font-bold text-[10px] uppercase tracking-wider">{{ $article->category }}</span>
                            </div>
                            <div class="p-6 space-y-3">
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold block">{{ is_string($article->published_at) ? $article->published_at : $article->published_at->format('d F Y') }}</span>
                                <h3 class="font-bold text-base text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                                    {{ $article->title }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-450 line-clamp-3 leading-relaxed">
                                    {{ $article->content }}
                                </p>
                            </div>
                        </div>
                        <div class="p-6 pt-0">
                            <a href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                Baca Selengkapnya
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ================= GALERI SECTION ================= -->
    <section id="galeri" class="py-24 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 text-center">
            <div class="space-y-4">
                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">Galeri Kegiatan</span>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white font-display">Galeri Aktivitas Siswa</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto text-sm">Dokumentasi momen berharga, proses pembelajaran, serta kegiatan ekstrakurikuler di Madrasah.</p>
            </div>

            <!-- Masonry-like/Responsive Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 pt-6">
                @foreach($displayGalleries as $item)
                    <div class="relative group rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-slate-200">
                        <img src="{{ $item->file_path }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $item->title }}">
                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6 text-left">
                            <span class="text-white text-sm font-bold block">{{ $item->title }}</span>
                            <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $item->type === 'photo' ? 'Foto Kegiatan' : 'Video' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- ================= KONTAK SECTION ================= -->
    <section id="kontak" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 items-start">
                
                <div class="lg:col-span-5 space-y-6">
                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider">Kontak Kami</span>
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white font-display leading-tight">Hubungi Madrasah YATIA</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        Kami sangat senang mendengarkan pertanyaan Anda. Silakan hubungi kami melalui kontak resmi atau kunjungi sekretariat kami langsung.
                    </p>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-emerald-55 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-800 dark:text-white text-sm">Alamat Madrasah</span>
                                <span class="text-xs text-slate-500 dark:text-slate-405">Jl. Raya YATIA No. 45, Jakarta Barat, DKI Jakarta</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-emerald-55 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-800 dark:text-white text-sm">Telepon & WhatsApp</span>
                                <span class="text-xs text-slate-500 dark:text-slate-405">+62 812-3456-7890 (Sekretariat)</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-emerald-55 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-800 dark:text-white text-sm">Email Portal</span>
                                <span class="text-xs text-slate-500 dark:text-slate-405">info@yatia.sch.id</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 font-display">Kirim Pesan Langsung</h3>
                    <form class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" placeholder="Masukkan nama Anda" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-55">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</label>
                                <input type="email" placeholder="contoh@domain.com" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-55">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subjek</label>
                            <input type="text" placeholder="Pertanyaan umum / PPDB" class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-55">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pesan</label>
                            <textarea rows="4" placeholder="Tuliskan detail pesan Anda..." class="w-full bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-750 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-55 resize-none"></textarea>
                        </div>
                        <button type="button" class="w-full py-4 bg-emerald-600 hover:bg-emerald-55 text-white font-bold rounded-xl transition-all shadow-md shadow-emerald-55/10">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>


    <!-- ================= FOOTER COMPLETE ================= -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 text-sm mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid md:grid-cols-2 lg:grid-cols-4 gap-12">
            
            <!-- Col 1: Logo & Address -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 text-emerald-500 shrink-0">
                        <x-application-logo class="w-full h-full" />
                    </div>
                    <span class="text-white font-bold text-lg font-display">MADRASAH YATIA</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Menyelenggarakan pendidikan Islam yang modern, terpadu, dan unggul guna membentuk generasi Robbani berakhlak mulia.
                </p>
                <div class="text-xs space-y-1">
                    <span class="block text-slate-300 font-semibold">Sekretariat Utama:</span>
                    <span>Jl. Raya YATIA No. 45, Jakarta Barat, DKI Jakarta, 11620</span>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="space-y-4">
                <h4 class="text-white font-bold font-display uppercase tracking-widest text-xs border-l-2 border-emerald-55 pl-3">Tautan Cepat</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="#profil" class="hover:text-emerald-450 transition-colors">Profil Madrasah</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-450 transition-colors">Penerimaan Siswa Baru (PPDB)</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-emerald-450 transition-colors">E-Learning Portal</a></li>
                    <li><a href="#program" class="hover:text-emerald-455 transition-colors">Kurikulum Akademik</a></li>
                </ul>
            </div>

            <!-- Col 3: Contact & WA -->
            <div class="space-y-4">
                <h4 class="text-white font-bold font-display uppercase tracking-widest text-xs border-l-2 border-emerald-55 pl-3">Hubungi Kami</h4>
                <div class="text-xs space-y-2">
                    <span class="block">Telepon: +62 21-555-0123</span>
                    <span class="block">Email: <a href="mailto:info@yatia.sch.id" class="hover:text-emerald-450">info@yatia.sch.id</a></span>
                    <span class="block">WhatsApp: <a href="https://wa.me/6281234567890" target="_blank" class="text-emerald-400 hover:underline">+62 812-3456-7890</a></span>
                </div>
            </div>

            <!-- Col 4: Google Maps Embed -->
            <div class="space-y-4">
                <h4 class="text-white font-bold font-display uppercase tracking-widest text-xs border-l-2 border-emerald-55 pl-3">Lokasi Google Maps</h4>
                <div class="w-full h-32 rounded-2xl overflow-hidden border border-slate-800 shadow-inner">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m12!1m3!1d3966.241517596245!2d106.8202517!3d-6.2319082!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3fbba9173d1%3A0xc32a0c4f8d5cf65c!2sSenayan%2C%20Kebayoran%20Baru%2C%20South%20Jakarta%20City%2C%20Jakarta!5e0!3m2!1sen!2sid!4v1692120000000!5m2!1sen!2sid" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div>

        <!-- Copyright & Socials bottom bar -->
        <div class="bg-slate-950/80 py-6 border-t border-slate-800/80 text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <span>&copy; 2026 Yayasan Pendidikan YATIA. Hak Cipta Dilindungi Undang-Undang.</span>
                <div class="flex gap-4 items-center">
                    <a href="https://instagram.com" target="_blank" class="hover:text-emerald-500 font-semibold">Instagram</a>
                    <a href="https://facebook.com" target="_blank" class="hover:text-emerald-500 font-semibold">Facebook</a>
                    <a href="https://youtube.com" target="_blank" class="hover:text-emerald-500 font-semibold">YouTube</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scrollspy script to highlight active navbar section -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            function scrollSpy() {
                let current = 'beranda';
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
                        'border-emerald-600', 'text-emerald-600', 'dark:border-emerald-400', 'dark:text-emerald-400', 'font-semibold'
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