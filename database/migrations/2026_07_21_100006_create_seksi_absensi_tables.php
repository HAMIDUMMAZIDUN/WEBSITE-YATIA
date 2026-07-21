<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. attendance_officer_assignments
        Schema::create('attendance_officer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student officer user account
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. attendance_submissions
        Schema::create('attendance_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('status')->default('draft'); // draft, pending, verified, rejected
            $table->timestamps();
        });

        // 3. attendance_evidence
        Schema::create('attendance_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance_daily')->onDelete('cascade');
            $table->string('type'); // sakit, izin, dispensasi
            $table->string('file'); // File path
            $table->string('source')->nullable(); // e.g. upload_by_officer
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
        });

        // 4. attendance_verifications
        Schema::create('attendance_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('attendance_submissions')->onDelete('cascade');
            $table->foreignId('verifier_id')->constrained('users')->onDelete('cascade'); // Wali kelas
            $table->string('decision'); // Terima, Kembalikan, Ajukan Koreksi
            $table->text('note')->nullable();
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();
        });

        // 5. attendance_corrections
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance_daily')->onDelete('cascade');
            $table->string('old_status');
            $table->string('new_status');
            $table->text('reason');
            $table->string('evidence')->nullable();
            $table->string('decision')->default('pending'); // pending, approved, rejected
            $table->foreignId('decided_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 6. late_records
        Schema::create('late_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->date('date');
            $table->time('arrival_time');
            $table->integer('minutes')->default(0);
            $table->text('reason')->nullable();
            $table->text('action')->nullable();
            $table->timestamps();
        });

        // 7. dispensations
        Schema::create('dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('activity');
            $table->string('supervisor')->nullable();
            $table->timestamps();
        });

        // 8. attendance_audit_logs
        Schema::create('attendance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity'); // attendance_daily, attendance_submissions
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_audit_logs');
        Schema::dropIfExists('dispensations');
        Schema::dropIfExists('late_records');
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendance_verifications');
        Schema::dropIfExists('attendance_evidence');
        Schema::dropIfExists('attendance_submissions');
        Schema::dropIfExists('attendance_officer_assignments');
    }
};
