<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkCase extends Model
{
    protected $guarded = [];

    public function referral()
    {
        return $this->belongsTo(BkReferral::class, 'referral_id');
    }

    public function counselor()
    {
        return $this->belongsTo(Guru::class, 'counselor_id');
    }

    public function sessions()
    {
        return $this->hasMany(BkSession::class, 'case_id');
    }

    public function timeline()
    {
        return $this->hasMany(BkCaseTimeline::class, 'case_id');
    }

    public function schedules()
    {
        return $this->hasMany(BkSchedule::class, 'case_id');
    }

    public function externalReferrals()
    {
        return $this->hasMany(BkExternalReferral::class, 'case_id');
    }
}
