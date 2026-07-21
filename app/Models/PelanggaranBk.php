<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelanggaranBk extends Model
{
    protected $guarded = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guruBk()
    {
        return $this->belongsTo(Guru::class, 'guru_bk_id');
    }
}
