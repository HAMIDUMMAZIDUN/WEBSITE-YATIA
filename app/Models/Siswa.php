<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function pelanggaranBks()
    {
        return $this->hasMany(PelanggaranBk::class);
    }

    public function attendanceDaily()
    {
        return $this->hasMany(AttendanceDaily::class, 'student_id');
    }

    public function studentGuardians()
    {
        return $this->hasMany(StudentGuardian::class, 'student_id');
    }

    public function achievementsViolations()
    {
        return $this->hasMany(AchievementViolation::class, 'student_id');
    }

    public function studentInterventions()
    {
        return $this->hasMany(StudentIntervention::class, 'student_id');
    }

    public function promotionDecisions()
    {
        return $this->hasMany(PromotionDecision::class, 'student_id');
    }
}
