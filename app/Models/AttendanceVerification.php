<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceVerification extends Model
{
    protected $guarded = [];

    public function submission()
    {
        return $this->belongsTo(AttendanceSubmission::class, 'submission_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
