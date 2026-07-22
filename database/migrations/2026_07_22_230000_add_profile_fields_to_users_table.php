<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('nik');
            $table->string('photo_profile')->nullable()->after('password');
            $table->string('password_plain')->nullable()->after('photo_profile');
        });

        // Copy NIK to username as default
        \DB::table('users')->update([
            'username' => \DB::raw('nik')
        ]);

        // Copy plain passwords for gurus
        if (Schema::hasTable('gurus')) {
            $gurus = \DB::table('gurus')->get();
            foreach ($gurus as $guru) {
                \DB::table('users')->where('id', $guru->user_id)->update([
                    'password_plain' => $guru->password_plain
                ]);
            }
        }

        // Set default plain passwords for others
        \DB::table('users')->whereNull('password_plain')->update([
            'password_plain' => 'hamidun123'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'photo_profile', 'password_plain']);
        });
    }
};
