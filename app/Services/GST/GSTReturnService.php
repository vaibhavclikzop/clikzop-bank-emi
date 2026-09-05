<?php

namespace App\Services\GST;

use Illuminate\Support\Facades\Http;

class GSTReturnService
{
    public function sendOtp($username, $gst)
    {
       // $data = array("statusMessage" => "This is testing message", "statusCode" => 101,"request_id"=>"TESTINGKEY");

        $url = 'https://api.karza.in/gst/uat/v2/gst-return-auth-advance';


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-karza-key'  => config('services.karza.api_key_gst_itr'),
        ])->post($url, [
            'username'       => $username,
            'gstin'          => $gst,
            'refId'          => '',
            'consent'        => 'Y',
            'consolidate'    => false,
            'extendedPeriod' => false,
        ]);
        $data = $response->json();

        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }

        if ($data["statusCode"] != 101) {
            return [

                'status' => false,
                'message' => $data["statusMessage"],
                'request_id' => "",
                'data' => $data,
            ];
        }

        return [

            'status' => true,
            'message' => $data["statusMessage"],
            'request_id' => $data["requestId"],
            'data' => $data,
        ];
    }

    public function verifyOTP($otp, $request_id)
    {

        $data = [];
        $url = 'https://api.karza.in/gst/uat/v2/gst-return-auth-advance';


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-karza-key'  => config('services.karza.api_key_gst_itr'),
        ])->post($url, [
            'requestId'          => $request_id,
            'otp'       => $otp,
            "consent"   => "Y",

        ]);



        if (!$response->successful()) {
            return [
                'status'       => false,
                'http_status'  => $response->status(),
                'message'      => 'API request failed',
                'response'     => $response->json(),
                'body'         => $response->body(),
            ];
        }
        return [

            'status' => true,
            'message' => 'Verified successfully',
            'data' => $data,
        ];
    }
}
