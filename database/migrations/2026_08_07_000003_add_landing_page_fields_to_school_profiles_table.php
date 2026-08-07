<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->string('jadwal_shalat_kota')->nullable()->default('DKI Jakarta & Sekitarnya');
            $table->text('kutipan_teks')->nullable();
            $table->string('kutipan_sumber')->nullable();
            $table->string('counter_siswa')->nullable()->default('850+');
            $table->string('counter_guru')->nullable()->default('45+');
            $table->string('counter_ekskul')->nullable()->default('18+');
            $table->string('counter_penghargaan')->nullable()->default('35+');
            $table->text('keunggulan_json')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('deskripsi_singkat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'jadwal_shalat_kota',
                'kutipan_teks',
                'kutipan_sumber',
                'counter_siswa',
                'counter_guru',
                'counter_ekskul',
                'counter_penghargaan',
                'keunggulan_json',
                'visi',
                'misi',
                'deskripsi_singkat'
            ]);
        });
    }
};
