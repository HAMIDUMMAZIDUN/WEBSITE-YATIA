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
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('user_id');
            $table->string('nik_nuptk')->nullable()->after('nip');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('pendidikan')->nullable()->after('tanggal_lahir');
            $table->string('password_plain')->nullable()->after('pendidikan');
            $table->integer('jtm')->default(0)->after('password_plain');
            $table->boolean('is_aktif')->default(true)->after('jtm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn([
                'foto',
                'nik_nuptk',
                'tempat_lahir',
                'tanggal_lahir',
                'pendidikan',
                'password_plain',
                'jtm',
                'is_aktif'
            ]);
        });
    }
};
