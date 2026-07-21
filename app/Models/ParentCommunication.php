<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentCommunication extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function guardian()
    {
        return $this->belongsTo(StudentGuardian::class, 'guardian_id');
    }
}
