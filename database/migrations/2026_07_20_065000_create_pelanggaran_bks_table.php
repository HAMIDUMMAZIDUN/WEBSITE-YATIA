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
        Schema::create('pelanggaran_bks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guru_bk_id')->constrained('gurus')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('pelanggaran');
            $table->integer('poin')->default(0);
            $table->text('tindakan')->nullable();
            $table->string('status')->default('diproses'); // 'diproses', 'diselesaikan', 'panggilan_orang_tua'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_bks');
    }
};
