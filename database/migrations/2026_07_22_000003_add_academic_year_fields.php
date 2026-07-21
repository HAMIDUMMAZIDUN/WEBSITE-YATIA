<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->string('hari_sekolah')->default('Monday,Tuesday,Wednesday,Thursday,Friday,Saturday')->after('tanggal_selesai');
            $table->boolean('locked')->default(false)->after('hari_sekolah');
            $table->string('unit')->default('Semua')->after('locked'); // MTs, MA, Semua
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai', 'hari_sekolah', 'locked', 'unit']);
        });
    }
};
