<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPVerifications extends Model
{
    protected $table = "otp_verifications";
    protected $primaryKey = 'id';
    protected $fillable = [
        "verification_id",
        "mobile",
        "purpose",
        "otp_hash",
        "attempts",
        "max_attempts",
        "expires_at",
        "verified_at",
        "last_sent_at",
        "provider",
        "provider_message_id",
        "ip_address",
        "user_agent",
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];
}
