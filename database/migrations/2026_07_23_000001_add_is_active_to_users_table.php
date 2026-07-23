<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password_plain');
        });

        // Sync existing guru status → users.is_active
        if (Schema::hasTable('gurus')) {
            $gurus = \DB::table('gurus')->get();
            foreach ($gurus as $guru) {
                \DB::table('users')->where('id', $guru->user_id)->update([
                    'is_active' => (bool) $guru->is_aktif,
                ]);
            }
        }

        // Sync existing siswa status → users.is_active
        if (Schema::hasTable('siswas')) {
            $siswas = \DB::table('siswas')->get();
            foreach ($siswas as $siswa) {
                \DB::table('users')->where('id', $siswa->user_id)->update([
                    'is_active' => $siswa->status === 'Aktif' ? 1 : 0,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
