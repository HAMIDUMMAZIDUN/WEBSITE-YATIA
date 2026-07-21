<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDecision extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function sourceClass()
    {
        return $this->belongsTo(Kelas::class, 'source_class');
    }

    public function targetClass()
    {
        return $this->belongsTo(Kelas::class, 'target_class');
    }
}
