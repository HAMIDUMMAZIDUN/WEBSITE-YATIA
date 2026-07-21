<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Hanya menyemai data peran (roles) yang diperlukan sistem.
     * Akun pengguna dibuat langsung melalui panel Super Admin.
     */
    public function run(): void
    {
        // Seed Roles — diperlukan agar sistem RBAC berjalan
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin'],
            ['name' => 'guru',        'display_name' => 'Guru Mata Pelajaran'],
            ['name' => 'wali_kelas',  'display_name' => 'Wali Kelas'],
            ['name' => 'siswa',       'display_name' => 'Siswa'],
            ['name' => 'guru_bk',     'display_name' => 'Guru BK'],
            ['name' => 'wali_kelas_guru', 'display_name' => 'Wali Kelas & Guru Mapel'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            \App\Models\User::firstOrCreate(
                ['email' => 'hamidabdulaziz36@gmail.com'],
                [
                    'name' => 'Hamid Abdul Aziz',
                    'password' => \Illuminate\Support\Facades\Hash::make('hamidun123'),
                    'role_id' => $superAdminRole->id,
                ]
            );
        }
    }
}
