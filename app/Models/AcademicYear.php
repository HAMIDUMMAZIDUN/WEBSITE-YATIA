<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $guarded = [];

    public function classHomerooms()
    {
        return $this->hasMany(ClassHomeroom::class);
    }

    public function holidays()
    {
        return $this->hasMany(AcademicHoliday::class);
    }
}
