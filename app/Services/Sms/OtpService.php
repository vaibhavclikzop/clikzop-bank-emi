<?php

namespace App\Services\Sms;


use App\Models\OTPVerifications;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function send(string $mobile, string $purpose): array
    {
        // Invalidate previous unverified OTPs
        OTPVerifications::where('mobile', $mobile)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->update([
                'verified_at' => now(),
            ]);

        // Fixed OTP for development/testing
        $otp = '123456';

        $verificationId = (string) Str::uuid();

        $otpVerification = OTPVerifications::create([
            'verification_id' => $verificationId,
            'mobile' => $mobile,
            'purpose' => $purpose,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'max_attempts' => config('otp.max_attempts', 5),
            'expires_at' => now()->addMinutes(
                config('otp.expiry_minutes', 5)
            ),
            'last_sent_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'verification_id' => $otpVerification->verification_id,
            'expires_at' => $otpVerification->expires_at,
        ];
    }

    public function verify(string $verificationId, string $otp): array {

        $verification = OTPVerifications::where(
            'verification_id',
            $verificationId
        )->first();

        if (!$verification) {
            throw ValidationException::withMessages([
                'verification_id' => ['Invalid verification request.'],
            ]);
        }

        if ($verification->verified_at) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has already been verified.'],
            ]);
        }

        if ($verification->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired.'],
            ]);
        }

        if ($verification->attempts >= $verification->max_attempts) {
            throw ValidationException::withMessages([
                'otp' => ['Maximum OTP attempts exceeded.'],
            ]);
        }

        // Increment attempt before verification
        $verification->increment('attempts');

        if (!Hash::check($otp, $verification->otp_hash)) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP.'],
            ]);
        }

        $verification->update([
            'verified_at' => now(),
        ]);

        return [
            'verification_id' => $verification->verification_id,
            'verified' => true,
            'verified_at' => $verification->verified_at,
        ];
    }
}
