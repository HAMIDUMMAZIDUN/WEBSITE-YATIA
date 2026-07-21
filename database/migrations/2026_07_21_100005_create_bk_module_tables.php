<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. bk_cases
        Schema::create('bk_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->nullable()->constrained('bk_referrals')->onDelete('set null');
            $table->foreignId('counselor_id')->constrained('gurus')->onDelete('cascade'); // BK Counselor is a Guru
            $table->text('goal')->nullable();
            $table->text('plan')->nullable();
            $table->string('priority')->default('sedang');
            $table->string('status')->default('terbuka'); // terbuka, diproses, selesai, dirujuk_eksternal
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // 2. bk_sessions
        Schema::create('bk_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('bk_cases')->onDelete('cascade');
            $table->string('service_type'); // Konseling Individual, Konseling Kelompok, Bimbingan Karir
            $table->date('date');
            $table->integer('duration'); // in minutes
            $table->text('objective');
            $table->text('summary'); // Very confidential summary
            $table->text('follow_up')->nullable();
            $table->string('privacy_level')->default('confidential'); // public, internal_school, confidential, strict_confidential
            $table->timestamps();
        });

        // 3. bk_case_timeline
        Schema::create('bk_case_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('bk_cases')->onDelete('cascade');
            $table->string('event_type'); // rujukan, sesi, rujukan_eksternal, catatan, penutupan
            $table->date('event_date');
            $table->text('note');
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            $table->string('visibility')->default('bk_only'); // bk_only, shared_homeroom
            $table->timestamps();
        });

        // 4. bk_schedules
        Schema::create('bk_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->nullable()->constrained('bk_cases')->onDelete('cascade');
            $table->string('service_type');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('room')->nullable();
            $table->string('participants')->nullable(); // comma-separated or json
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->timestamps();
        });

        // 5. bk_parent_meetings
        Schema::create('bk_parent_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('student_guardians')->onDelete('cascade');
            $table->text('purpose');
            $table->dateTime('schedule');
            $table->text('attendees')->nullable();
            $table->text('result')->nullable();
            $table->boolean('consent')->default(false); // Agreement/consent obtained
            $table->timestamps();
        });

        // 6. bk_external_referrals
        Schema::create('bk_external_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('bk_cases')->onDelete('cascade');
            $table->string('institution'); // e.g., Puskesmas, Psikolog, Kepolisian
            $table->text('purpose');
            $table->boolean('consent')->default(false); // parent consent
            $table->text('shared_documents')->nullable(); // shared details
            $table->string('status')->default('pending'); // pending, sent, accepted, completed
            $table->timestamps();
        });

        // 7. audit_logs (Strict tracking for BK access)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // view, read_session, update_case, download_pdf, encrypt
            $table->string('entity'); // bk_cases, bk_sessions, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('bk_external_referrals');
        Schema::dropIfExists('bk_parent_meetings');
        Schema::dropIfExists('bk_schedules');
        Schema::dropIfExists('bk_case_timeline');
        Schema::dropIfExists('bk_sessions');
        Schema::dropIfExists('bk_cases');
    }
};
