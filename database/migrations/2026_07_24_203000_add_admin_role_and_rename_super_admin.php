<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update super_admin display name
        DB::table('roles')
            ->where('name', 'super_admin')
            ->update(['display_name' => 'Super Admin']);

        // 2. Insert admin role if not exists
        $exists = DB::table('roles')->where('name', 'admin')->exists();
        if (!$exists) {
            DB::table('roles')->insert([
                'name' => 'admin',
                'display_name' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'super_admin')
            ->update(['display_name' => 'Admin']);

        DB::table('roles')
            ->where('name', 'admin')
            ->delete();
    }
};
