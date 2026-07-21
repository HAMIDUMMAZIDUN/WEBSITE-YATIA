<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceEvidence extends Model
{
    protected $table = 'attendance_evidence';
    protected $guarded = [];

    public function attendance()
    {
        return $this->belongsTo(AttendanceDaily::class, 'attendance_id');
    }
}
