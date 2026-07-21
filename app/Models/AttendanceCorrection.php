<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $guarded = [];

    public function attendance()
    {
        return $this->belongsTo(AttendanceDaily::class, 'attendance_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
