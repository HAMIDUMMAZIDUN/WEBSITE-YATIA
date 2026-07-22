<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\SchoolProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class ThemeSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database to have roles, classes, profile, etc.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_welcome_page_initially_renders_with_database_light_theme()
    {
        $profile = SchoolProfile::firstOrCreate(['id' => 1]);
        $profile->update(['theme_mode' => 'light']);

        $response = $this->get('/');
        $response->assertOk();
        // Check that the class="dark" is not on the html/body tag
        $response->assertDontSee('class="dark"', false);
    }

    public function test_welcome_page_renders_with_database_dark_theme()
    {
        $profile = SchoolProfile::find(1);
        $profile->theme_mode = 'dark';
        $profile->save();

        $this->assertEquals('dark', SchoolProfile::find(1)->theme_mode);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('class="dark"', false);
    }

    public function test_superadmin_can_update_theme_mode()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        $profile = SchoolProfile::firstOrCreate(['id' => 1]);
        $profile->update(['theme_mode' => 'light']);

        // Update profile including theme_mode
        $response = $this->actingAs($admin)->post('/admin/profil-madrasah', [
            'nama_yayasan' => 'Yayasan Tarbiyah Islamiyah Al-Azhar (YATIA)',
            'nama_mts' => 'MTs YATIA',
            'nama_ma' => 'MA YATIA',
            'status_sekolah' => 'Swasta',
            'akreditasi' => 'A',
            'alamat' => 'Jl. Pahlawan No. 45, Jakarta Selatan',
            'telepon' => '0217894561',
            'email' => 'info@yatia.sch.id',
            'website' => 'https://yatia.sch.id',
            'theme_mode' => 'dark',
            'alasan_perubahan' => 'Mengubah tema sistem ke mode gelap',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $profile->refresh();
        $this->assertEquals('dark', $profile->theme_mode);

        // Now assert the theme is loaded on welcome page
        $welcomeResponse = $this->get('/');
        $welcomeResponse->assertOk();
        $welcomeResponse->assertSee('class="dark"', false);

        // Assert the theme is loaded on dashboard page
        $dashboardResponse = $this->actingAs($admin)->get('/admin/guru');
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('class="dark"', false);
    }
}
