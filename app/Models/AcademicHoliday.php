<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicHoliday extends Model
{
    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
