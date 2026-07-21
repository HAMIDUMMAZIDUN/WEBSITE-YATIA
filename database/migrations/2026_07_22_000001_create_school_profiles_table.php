<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_yayasan')->nullable();
            $table->string('nama_mts')->nullable();
            $table->string('nama_ma')->nullable();
            $table->string('nsm_mts')->nullable();
            $table->string('nsm_ma')->nullable();
            $table->string('npsn_mts')->nullable();
            $table->string('npsn_ma')->nullable();
            $table->string('status_sekolah')->nullable(); // e.g. "Swasta"
            $table->string('akreditasi')->nullable(); // e.g. "A"
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('pimpinan_mts')->nullable();
            $table->string('pimpinan_ma')->nullable();
            $table->string('nip_pimpinan_mts')->nullable();
            $table->string('nip_pimpinan_ma')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('stempel_path')->nullable();
            $table->string('tanda_tangan_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};
