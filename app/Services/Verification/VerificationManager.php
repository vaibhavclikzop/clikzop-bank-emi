<?php

namespace App\Services\Verification;

class VerificationManager
{
    public function verify($type, $document, $dob)
    {
        $map = [
            'pan' => PanService::class,
            'driving_license' => DlService::class,
            'passport' => PassportService::class,
            'aadhaar' => AadhaarService::class,

            'voter_id' => VoterService::class,

        ];

        if (! isset($map[$type])) {
            throw new \Exception('Invalid document type');
        }

        return app($map[$type])->verify($document, $dob);
    }
}
