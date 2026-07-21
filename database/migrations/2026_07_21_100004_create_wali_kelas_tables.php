<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. class_homerooms
        Schema::create('class_homerooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('gurus')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. student_guardians
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->string('name');
            $table->string('relation'); // e.g., Ayah, Ibu, Wali
            $table->string('phone')->nullable();
            $table->boolean('primary_contact')->default(false);
            $table->timestamps();
        });

        // 3. attendance_daily
        Schema::create('attendance_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->date('date');
            $table->string('status'); // H, S, I, A, T, D (Hadir, Sakit, Izin, Alpa, Terlambat, Dispensasi)
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->foreignId('input_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('evidence')->nullable(); // File path for sick/leave letters
            $table->string('verification_status')->default('draft'); // draft, pending, verified, rejected
            $table->timestamps();
        });

        // 4. homeroom_journals
        Schema::create('homeroom_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('siswas')->onDelete('cascade');
            $table->string('category'); // Pembinaan, Kunjungan Rumah, Kejadian Khusus
            $table->text('note');
            $table->text('action')->nullable();
            $table->string('status')->default('draft'); // draft, final
            $table->string('privacy')->default('public'); // public, internal_school, confidential
            $table->timestamps();
        });

        // 5. academic_snapshots
        Schema::create('academic_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('mata_pelajarans')->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->string('kktp_status'); // tuntas, tidak_tuntas
            $table->string('period'); // e.g., UTS, UAS, Semester_1
            $table->timestamps();
        });

        // 6. student_interventions
        Schema::create('student_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->text('issue');
            $table->text('target');
            $table->text('action');
            $table->foreignId('owner')->constrained('users')->onDelete('cascade'); // Wali kelas/Guru
            $table->date('due_date')->nullable();
            $table->text('result')->nullable();
            $table->timestamps();
        });

        // 7. achievements_violations
        Schema::create('achievements_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->string('type'); // prestasi, pelanggaran
            $table->string('category');
            $table->integer('points')->default(0);
            $table->string('evidence')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();
        });

        // 8. bk_referrals
        Schema::create('bk_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->string('source_type')->default('wali_kelas'); // wali_kelas, guru_mapel, otomatis
            $table->text('reason');
            $table->string('category')->nullable();
            $table->string('priority')->default('sedang'); // rendah, sedang, tinggi, darurat
            $table->string('status')->default('pending'); // pending, diproses, selesai, dirujuk_eksternal
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('summary_for_homeroom')->nullable(); // Brief notes shared with homeroom teacher
            $table->timestamps();
        });

        // 9. parent_communications
        Schema::create('parent_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('student_guardians')->onDelete('cascade');
            $table->string('channel'); // WhatsApp, Email, Surat, Pertemuan
            $table->text('message');
            $table->string('status')->default('sent'); // sent, read, replied
            $table->text('response')->nullable();
            $table->timestamps();
        });

        // 10. promotion_decisions
        Schema::create('promotion_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('source_class')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('target_class')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->text('recommendation')->nullable();
            $table->string('decision'); // naik, tinggal, lulus, alumni
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_decisions');
        Schema::dropIfExists('parent_communications');
        Schema::dropIfExists('bk_referrals');
        Schema::dropIfExists('achievements_violations');
        Schema::dropIfExists('student_interventions');
        Schema::dropIfExists('academic_snapshots');
        Schema::dropIfExists('homeroom_journals');
        Schema::dropIfExists('attendance_daily');
        Schema::dropIfExists('student_guardians');
        Schema::dropIfExists('class_homerooms');
    }
};
