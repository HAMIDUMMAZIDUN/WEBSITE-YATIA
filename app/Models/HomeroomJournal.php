<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeroomJournal extends Model
{
    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }
}
