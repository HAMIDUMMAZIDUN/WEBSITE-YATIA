<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSubmission extends Model
{
    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifications()
    {
        return $this->hasMany(AttendanceVerification::class, 'submission_id');
    }
}
