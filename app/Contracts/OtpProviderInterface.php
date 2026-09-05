<?php

namespace App\Contracts;

interface OtpProviderInterface
{
    public function send(
        string $mobile,
        string $otp,
        string $purpose
    ): array;
}
