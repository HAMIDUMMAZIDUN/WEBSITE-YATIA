<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran')->unique();
            $table->string('nama_lengkap');
            $table->string('nisn')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('asal_sekolah');
            $table->string('nomor_wa');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ppdb_registrations');
    }
};
