<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $guarded = [];

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function materiTugas()
    {
        return $this->hasMany(MateriTugas::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
