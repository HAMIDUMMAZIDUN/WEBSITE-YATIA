<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class GuruAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database to have roles, classes, profile, etc.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_guru_index()
    {
        $response = $this->get('/admin/guru');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_guru_index()
    {
        // Get a teacher user
        $role = Role::where('name', 'guru')->first();
        $user = User::where('role_id', $role->id)->first();

        $response = $this->actingAs($user)->get('/admin/guru');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_guru_index()
    {
        $role = Role::where('name', 'super_admin')->first();
        $user = User::where('role_id', $role->id)->first();

        $response = $this->actingAs($user)->get('/admin/guru');
        $response->assertOk();
        $response->assertSee('Ahmad Ariyadi');
        $response->assertSee('Ai Siriyah Nila');
    }

    public function test_admin_can_store_guru()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        $teacherRole = Role::where('name', 'guru')->first();

        $response = $this->actingAs($admin)->post('/admin/guru', [
            'name' => 'New Teacher, S.Pd.',
            'email' => 'new.teacher@gmail.com',
            'password' => 'secret123',
            'role_id' => $teacherRole->id,
            'jenis_kelamin' => 'L',
            'nik_nuptk' => '1234567890123456',
            'tempat_lahir' => 'BANDUNG',
            'tanggal_lahir' => '1990-01-01',
            'pendidikan' => 'Sarjana (S1)',
            'jtm' => 24,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'new.teacher@gmail.com']);
        $this->assertDatabaseHas('gurus', [
            'nama' => 'New Teacher, S.Pd.',
            'nik_nuptk' => '1234567890123456',
            'tempat_lahir' => 'BANDUNG',
        ]);
    }

    public function test_admin_can_update_guru()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        $guru = Guru::first();
        $teacherRole = Role::where('name', 'guru')->first();

        $response = $this->actingAs($admin)->patch('/admin/guru/' . $guru->id, [
            'name' => 'Updated Name, M.Pd.',
            'email' => $guru->user->email,
            'role_id' => $teacherRole->id,
            'jenis_kelamin' => 'P',
            'nik_nuptk' => '9999999999999999',
            'tempat_lahir' => 'JAKARTA',
            'tanggal_lahir' => '1985-05-05',
            'pendidikan' => 'Magister (S2)',
            'jtm' => 40,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $guru->refresh();
        $this->assertEquals('Updated Name, M.Pd.', $guru->nama);
        $this->assertEquals('P', $guru->jenis_kelamin);
        $this->assertEquals('9999999999999999', $guru->nik_nuptk);
    }

    public function test_admin_can_toggle_guru_status()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        $guru = Guru::first();
        $this->assertTrue((bool)$guru->is_aktif);

        $response = $this->actingAs($admin)->post('/admin/guru/' . $guru->id . '/toggle-status');
        $response->assertRedirect();

        $guru->refresh();
        $this->assertFalse((bool)$guru->is_aktif);
    }

    public function test_admin_can_delete_guru()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        $guru = Guru::first();
        $guruUserId = $guru->user_id;

        $response = $this->actingAs($admin)->delete('/admin/guru/' . $guru->id);
        $response->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $guruUserId]);
        $this->assertDatabaseMissing('gurus', ['id' => $guru->id]);
    }
}
