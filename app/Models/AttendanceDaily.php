<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDaily extends Model
{
    protected $table = 'attendance_daily';
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function evidence()
    {
        return $this->hasMany(AttendanceEvidence::class, 'attendance_id');
    }
}
