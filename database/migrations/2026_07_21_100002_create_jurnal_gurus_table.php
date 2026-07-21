<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajarans')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('materi');
            $table->json('kehadiran_summary')->nullable(); // JSON object of student presence
            $table->text('tindak_lanjut')->nullable();
            $table->string('status')->default('draft'); // draft, final
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_gurus');
    }
};
