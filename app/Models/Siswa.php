<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function pelanggaranBks()
    {
        return $this->hasMany(PelanggaranBk::class);
    }
}
