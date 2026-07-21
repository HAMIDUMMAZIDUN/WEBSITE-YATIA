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
        Schema::table('users', function (Blueprint $table) {
            // NIK: universal login identifier (replaces email as login field)
            // Nullable first so existing rows don't break; we'll populate it afterward.
            $table->string('nik')->nullable()->unique()->after('role_id')
                  ->comment('Nomor Induk Kependudukan / NIS / NIP – used as login identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }
};
