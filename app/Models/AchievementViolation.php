<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementViolation extends Model
{
    protected $table = 'achievements_violations';
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }
}
