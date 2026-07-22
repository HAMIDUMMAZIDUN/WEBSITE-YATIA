<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Banner;
use App\Models\SocialMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database to have roles, etc.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_publications()
    {
        $this->get('/admin/publikasi/berita')->assertRedirect('/login');
        $this->get('/admin/publikasi/galeri')->assertRedirect('/login');
        $this->get('/admin/publikasi/medsos')->assertRedirect('/login');
        $this->get('/admin/publikasi/banner')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_publications()
    {
        $role = Role::where('name', 'guru')->first();
        $user = User::where('role_id', $role->id)->first();

        $this->actingAs($user)->get('/admin/publikasi/berita')->assertStatus(403);
    }

    public function test_admin_can_manage_berita()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // 1. Create Article
        Storage::fake('public');
        $file = UploadedFile::fake()->image('news.jpg');

        $response = $this->actingAs($admin)->post('/admin/publikasi/berita', [
            'title' => 'Berita Utama Hari Ini',
            'category' => 'Akademik',
            'content' => 'Ini adalah isi konten berita yang sangat penting.',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'image' => $file
        ]);

        $response->assertRedirect('/admin/publikasi/berita');
        $this->assertDatabaseHas('articles', [
            'title' => 'Berita Utama Hari Ini',
            'category' => 'Akademik'
        ]);

        $article = Article::first();

        // 2. Edit Article
        $response = $this->actingAs($admin)->patch('/admin/publikasi/berita/' . $article->id, [
            'title' => 'Judul Berita Terupdate',
            'category' => 'Agenda',
            'content' => 'Konten berita yang sudah direvisi.',
            'published_at' => now()->format('Y-m-d H:i:s')
        ]);

        $response->assertRedirect('/admin/publikasi/berita');
        $this->assertDatabaseHas('articles', [
            'title' => 'Judul Berita Terupdate',
            'category' => 'Agenda'
        ]);

        // 3. Delete Article
        $response = $this->actingAs($admin)->delete('/admin/publikasi/berita/' . $article->id);
        $response->assertRedirect('/admin/publikasi/berita');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_can_manage_galeri()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // 1. Create Photo
        Storage::fake('public');
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($admin)->post('/admin/publikasi/galeri', [
            'title' => 'Foto Kegiatan Belajar',
            'type' => 'photo',
            'file' => $file
        ]);

        $response->assertRedirect('/admin/publikasi/galeri');
        $this->assertDatabaseHas('galleries', [
            'title' => 'Foto Kegiatan Belajar',
            'type' => 'photo'
        ]);

        $gallery = Gallery::first();

        // 2. Edit Gallery
        $response = $this->actingAs($admin)->patch('/admin/publikasi/galeri/' . $gallery->id, [
            'title' => 'Foto Kegiatan Belajar MA',
            'type' => 'photo'
        ]);

        $response->assertRedirect('/admin/publikasi/galeri');
        $this->assertDatabaseHas('galleries', [
            'title' => 'Foto Kegiatan Belajar MA'
        ]);

        // 3. Delete Gallery
        $response = $this->actingAs($admin)->delete('/admin/publikasi/galeri/' . $gallery->id);
        $response->assertRedirect('/admin/publikasi/galeri');
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    public function test_admin_can_manage_banner()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // 1. Create Banner
        Storage::fake('public');
        $file = UploadedFile::fake()->image('banner.jpg');

        $response = $this->actingAs($admin)->post('/admin/publikasi/banner', [
            'title' => 'Banner Selamat Datang',
            'subtitle' => 'Selamat datang di SIMS YATIA',
            'order' => 1,
            'link_url' => 'https://example.com',
            'image' => $file
        ]);

        $response->assertRedirect('/admin/publikasi/banner');
        $this->assertDatabaseHas('banners', [
            'title' => 'Banner Selamat Datang',
            'order' => 1
        ]);

        $banner = Banner::first();

        // 2. Edit Banner
        $response = $this->actingAs($admin)->patch('/admin/publikasi/banner/' . $banner->id, [
            'title' => 'Banner Penerimaan Siswa Baru',
            'subtitle' => 'Daftar sekarang juga!',
            'order' => 2,
            'is_active' => '1'
        ]);

        $response->assertRedirect('/admin/publikasi/banner');
        $this->assertDatabaseHas('banners', [
            'title' => 'Banner Penerimaan Siswa Baru',
            'order' => 2,
            'is_active' => true
        ]);

        // 3. Delete Banner
        $response = $this->actingAs($admin)->delete('/admin/publikasi/banner/' . $banner->id);
        $response->assertRedirect('/admin/publikasi/banner');
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_admin_can_manage_medsos()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // Ensure we have some social media records (the seeder creates them)
        $social = SocialMedia::first();

        $response = $this->actingAs($admin)->post('/admin/publikasi/medsos', [
            'urls' => [
                $social->id => 'https://facebook.com/yatia_updated'
            ],
            'active' => [
                $social->id => '1'
            ]
        ]);

        $response->assertRedirect('/admin/publikasi/medsos');
        $this->assertDatabaseHas('social_media', [
            'id' => $social->id,
            'url' => 'https://facebook.com/yatia_updated',
            'is_active' => true
        ]);
    }
}
