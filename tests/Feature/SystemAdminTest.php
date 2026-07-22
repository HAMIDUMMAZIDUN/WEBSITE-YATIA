<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class SystemAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_cannot_access_system_routes()
    {
        $this->get('/admin/sistem/logs')->assertRedirect('/login');
        $this->get('/admin/sistem/audit')->assertRedirect('/login');
        $this->get('/admin/sistem/backup')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_system_routes()
    {
        $role = Role::where('name', 'guru')->first();
        $user = User::where('role_id', $role->id)->first();

        $this->actingAs($user)->get('/admin/sistem/logs')->assertStatus(403);
        $this->actingAs($user)->get('/admin/sistem/audit')->assertStatus(403);
        $this->actingAs($user)->get('/admin/sistem/backup')->assertStatus(403);
    }

    public function test_admin_can_access_logs_and_audit()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // Create dummy log
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'test_action',
            'entity' => 'test_table',
            'new_value' => '{"foo":"bar"}'
        ]);

        $this->actingAs($admin)->get('/admin/sistem/logs')
            ->assertStatus(200)
            ->assertSee('test_action');

        $this->actingAs($admin)->get('/admin/sistem/audit')
            ->assertStatus(200)
            ->assertSee('test_action');
    }

    public function test_admin_can_export_audit_logs()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'export_test',
            'entity' => 'users',
        ]);

        $responseCsv = $this->actingAs($admin)->get('/admin/sistem/audit/export/csv');
        $responseCsv->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseCsv->headers->get('Content-Type'));
        $this->assertStringContainsString('export_test', $responseCsv->getContent());

        $responseJson = $this->actingAs($admin)->get('/admin/sistem/audit/export/json');
        $responseJson->assertStatus(200);
        $this->assertStringContainsString('application/json', $responseJson->headers->get('Content-Type'));
        $this->assertStringContainsString('export_test', $responseJson->getContent());
    }

    public function test_admin_can_clear_audit_logs()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'soon_to_be_deleted',
            'entity' => 'users',
        ]);

        // Clear older than 30 days
        $response = $this->actingAs($admin)->post('/admin/sistem/audit/clear', [
            'clear_option' => 'older_30'
        ]);
        $response->assertRedirect('/admin/sistem/audit');
        // Our newly created log should still be there because it is not older than 30 days
        $this->assertDatabaseHas('audit_logs', ['action' => 'soon_to_be_deleted']);

        // Clear all logs
        $response = $this->actingAs($admin)->post('/admin/sistem/audit/clear', [
            'clear_option' => 'all'
        ]);
        $response->assertRedirect('/admin/sistem/audit');
        // All original logs are truncated, but one log indicating the clear action was added.
        $this->assertDatabaseMissing('audit_logs', ['action' => 'soon_to_be_deleted']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'clear_all_audit_logs']);
    }

    public function test_admin_can_create_delete_backup()
    {
        $role = Role::where('name', 'super_admin')->first();
        $admin = User::where('role_id', $role->id)->first();

        // Create backup
        $response = $this->actingAs($admin)->post('/admin/sistem/backup/create');
        $response->assertRedirect('/admin/sistem/backup');
        
        $backupDir = storage_path('app/backups');
        $files = scandir($backupDir);
        $backupFile = null;
        foreach ($files as $file) {
            if (str_starts_with($file, 'backup_')) {
                $backupFile = $file;
                break;
            }
        }
        
        $this->assertNotNull($backupFile);

        // Download backup
        $responseDownload = $this->actingAs($admin)->get('/admin/sistem/backup/download/' . $backupFile);
        $responseDownload->assertStatus(200);

        // Delete backup
        $responseDelete = $this->actingAs($admin)->delete('/admin/sistem/backup/' . $backupFile);
        $responseDelete->assertRedirect('/admin/sistem/backup');
        $this->assertFileDoesNotExist($backupDir . '/' . $backupFile);
    }
}
