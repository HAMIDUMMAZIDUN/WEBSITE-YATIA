<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($kelas) {
            $activeYear = \App\Models\AcademicYear::where('active', true)->first();
            if ($activeYear) {
                if ($kelas->wali_kelas_id) {
                    \App\Models\ClassHomeroom::updateOrCreate(
                        [
                            'academic_year_id' => $activeYear->id,
                            'class_id' => $kelas->id,
                        ],
                        [
                            'teacher_id' => $kelas->wali_kelas_id,
                            'active' => true,
                        ]
                    );
                } else {
                    \App\Models\ClassHomeroom::where('academic_year_id', $activeYear->id)
                        ->where('class_id', $kelas->id)
                        ->delete();
                }
            }
        });

        static::deleted(function ($kelas) {
            \App\Models\ClassHomeroom::where('class_id', $kelas->id)->delete();
        });
    }

    public function waliGuru()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function materiTugas()
    {
        return $this->hasMany(MateriTugas::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
}
