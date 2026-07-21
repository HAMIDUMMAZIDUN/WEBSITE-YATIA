<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

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

    public function pelanggaranBks()
    {
        return $this->hasMany(PelanggaranBk::class, 'guru_bk_id');
    }
}
