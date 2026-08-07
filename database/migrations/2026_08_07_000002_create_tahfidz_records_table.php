<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tahfidz_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->string('surah_juz');
            $table->string('ayat');
            $table->enum('predikat', ['A', 'B', 'C', 'D']);
            $table->date('tanggal_setoran');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tahfidz_records');
    }
};
