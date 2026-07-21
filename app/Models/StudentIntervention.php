<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentIntervention extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner');
    }
}
