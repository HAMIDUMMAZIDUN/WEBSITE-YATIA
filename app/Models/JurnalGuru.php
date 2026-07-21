<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalGuru extends Model
{
    protected $guarded = [];

    protected $casts = [
        'kehadiran_summary' => 'array',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }
}
