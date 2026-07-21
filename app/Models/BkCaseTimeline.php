<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkCaseTimeline extends Model
{
    protected $table = 'bk_case_timeline';
    protected $guarded = [];

    public function case()
    {
        return $this->belongsTo(BkCase::class, 'case_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
