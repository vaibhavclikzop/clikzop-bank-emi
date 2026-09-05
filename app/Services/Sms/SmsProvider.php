<?php

namespace App\Services\Sms;

use App\Contracts\OtpProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SmsProvider implements OtpProviderInterface
{
    public function send(string $mobile, string $otp, string $purpose): array
    {

        $response = Http::timeout(10)
            ->retry(2, 200)
            ->post(config('services.sms.url'), [
                'mobile' => $mobile,
                'otp' => $otp,
                'purpose' => $purpose,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to send OTP.'
            );
        }

        return [
            'success' => true,
            'message_id' => $response->json('message_id'),
        ];
    }
}
