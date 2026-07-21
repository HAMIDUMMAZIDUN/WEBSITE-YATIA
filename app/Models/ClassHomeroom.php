<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassHomeroom extends Model
{
    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Guru::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
