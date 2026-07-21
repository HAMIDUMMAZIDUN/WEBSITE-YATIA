<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkExternalReferral extends Model
{
    protected $guarded = [];

    public function case()
    {
        return $this->belongsTo(BkCase::class, 'case_id');
    }
}
