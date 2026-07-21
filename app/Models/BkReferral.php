<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkReferral extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cases()
    {
        return $this->hasMany(BkCase::class, 'referral_id');
    }
}
