<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $fillable = [
        'name','email','password','lang',
        'referral_code','used_referral_code',
        'industry','industry_other','referral_source','referral_other',
        'otp_hash','otp_expires_at','otp_attempts','otp_verified_at',
        'status','selected_plan_id','stripe_session_id',
        'ip','user_agent',
    ];

    protected $casts = [
        'otp_expires_at'  => 'datetime',
        'otp_verified_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'selected_plan_id');
    }
}
