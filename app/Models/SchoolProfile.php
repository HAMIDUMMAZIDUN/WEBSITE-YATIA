<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'nama_yayasan',
        'nama_mts',
        'nama_ma',
        'nsm_mts',
        'nsm_ma',
        'npsn_mts',
        'npsn_ma',
        'status_sekolah',
        'akreditasi',
        'alamat',
        'telepon',
        'email',
        'website',
        'pimpinan_mts',
        'pimpinan_ma',
        'nip_pimpinan_mts',
        'nip_pimpinan_ma',
        'logo_path',
        'stempel_path',
        'tanda_tangan_path',
    ];
}
