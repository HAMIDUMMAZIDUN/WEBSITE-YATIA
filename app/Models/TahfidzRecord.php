<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahfidzRecord extends Model
{
    protected $guarded = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
