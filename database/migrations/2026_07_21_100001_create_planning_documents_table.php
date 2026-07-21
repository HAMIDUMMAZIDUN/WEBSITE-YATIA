<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->string('type'); // CP, TP, ATP, Prota, Promes, KKTP, Modul Ajar
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->string('title');
            $table->longText('content'); // JSON or HTML draft content
            $table->string('status')->default('draft'); // draft, review, approved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_documents');
    }
};
