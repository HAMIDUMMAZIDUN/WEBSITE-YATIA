<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Articles (News & Announcements) Table
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['Akademik', 'Prestasi', 'Agenda', 'Umum'])->default('Umum');
            $table->string('image_path')->nullable();
            $table->dateTime('published_at');
            $table->timestamps();
        });

        // 2. Galleries Table
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['photo', 'video'])->default('photo');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->timestamps();
        });

        // 3. Social Media Table
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // Facebook, Instagram, YouTube, Twitter/X, TikTok
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Banners Table
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial default content
        DB::table('articles')->insert([
            [
                'title' => 'Persiapan Implementasi Kurikulum Merdeka Tahun Ajaran Baru',
                'content' => 'Rapat koordinasi guru menyepakati penyesuaian modul ajar dan sistem penilaian Kurikulum Merdeka terintegrasi di SIMS YATIA.',
                'category' => 'Akademik',
                'image_path' => null,
                'published_at' => '2026-07-20 09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Siswa YATIA Raih Juara 1 Olimpiade Fisika Tingkat Nasional',
                'content' => 'Selamat kepada tim Fisika sekolah atas pencapaian gemilang. Apresiasi besar diberikan untuk dedikasi siswa dan guru pembimbing.',
                'category' => 'Prestasi',
                'image_path' => null,
                'published_at' => '2026-07-18 10:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Jadwal Pengisian Rencana Pembelajaran Semester Ganjil',
                'content' => 'Diharapkan seluruh guru mata pelajaran untuk melengkapi jadwal dan silabus materi pelajaran di portal masing-masing sebelum 1 Agustus.',
                'category' => 'Agenda',
                'image_path' => null,
                'published_at' => '2026-07-15 08:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('galleries')->insert([
            [
                'title' => 'Ruang Kelas Modern',
                'type' => 'photo',
                'file_path' => null,
                'external_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Laboratorium IPA',
                'type' => 'photo',
                'file_path' => null,
                'external_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Perpustakaan Digital',
                'type' => 'photo',
                'file_path' => null,
                'external_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fasilitas Olahraga',
                'type' => 'photo',
                'file_path' => null,
                'external_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('social_media')->insert([
            [
                'platform' => 'Facebook',
                'url' => 'https://facebook.com/yatia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'Instagram',
                'url' => 'https://instagram.com/yatia.sch',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'YouTube',
                'url' => 'https://youtube.com/c/YATIATV',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'Twitter/X',
                'url' => 'https://x.com/yatia_sch',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'platform' => 'TikTok',
                'url' => 'https://tiktok.com/@yatia.official',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('banners')->insert([
            [
                'title' => 'Platform Digital Administrasi & Akademik YATIA',
                'subtitle' => 'Menghubungkan Super Admin, Guru, Wali Kelas, Siswa, dan Guru BK dalam satu ekosistem administrasi sekolah terpadu, modern, aman, dan mudah digunakan.',
                'image_path' => null,
                'link_url' => null,
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('social_media');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('articles');
    }
};
