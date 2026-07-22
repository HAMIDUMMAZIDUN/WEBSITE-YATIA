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
        Schema::table('kelas', function (Blueprint $table) {
            $table->enum('unit', ['MTs', 'MA', 'Semua'])->default('Semua')->after('wali_kelas_id');
        });

        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->enum('unit', ['MTs', 'MA', 'Semua'])->default('Semua')->after('kode_mapel');
        });

        // Populate existing classes
        foreach (Illuminate\Support\Facades\DB::table('kelas')->get() as $kelas) {
            $unit = preg_match('/^(X|XI|XII)/i', $kelas->nama_kelas) ? 'MA' : 'MTs';
            Illuminate\Support\Facades\DB::table('kelas')
                ->where('id', $kelas->id)
                ->update(['unit' => $unit]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('unit');
        });

        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
