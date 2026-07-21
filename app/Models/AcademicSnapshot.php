<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSnapshot extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(MataPelajaran::class, 'subject_id');
    }
}
