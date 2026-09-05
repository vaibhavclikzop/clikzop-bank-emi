<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;

class DlService
{
    public function verify($dl, $dob)
    {
        $dl = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $dl));

        $url = config('services.perfios.base_url').'/ssp/kyc/api/v3/dl';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-secure-id' => config('services.perfios.user_name'),
            'x-secure-cred' => config('services.perfios.client_password'),
            'x-organization-id' => config('services.perfios.client_id'),
        ])->post($url, [
            'dlNo' => $dl,
            'dob' => $dob,
            'additionalDetails' => true,
            'consent' => 'Y',
        ]);

        $data = $response->json();

        if (! $response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }
        $statusCode = $data['statusCode'] ?? null;

        if ($statusCode != 101) {

            return [
                'status' => false,
                'message' => 'Invalid Driving License or no data found',
                'data' => $data,
            ];
        }

        $result['name'] = $data['result']['name'] ?? 'NA';
        $result['gender'] = $data['result']['gender'] ?? 'NA';

        $result['dob'] = $dob;
        $result['status'] = $data['result']['status'];
        $result['city'] = $data['result']['address'][0]['city'] ?? 'NA';
        $result['district'] = $data['result']['address'][0]['district'] ?? 'NA';
        $result['state'] = $data['result']['address'][0]['state'] ?? 'NA';
        $result['country'] = $data['result']['address'][0]['country'] ?? 'NA';
        $result['pinCode'] = $data['result']['address'][0]['pin'] ?? 'NA';
        $result['address'] = ($data['result']['address'][0]['completeAddress'] ?? 'NA');

        return [

            'status' => true,
            'message' => 'DL verified successfully',
            'data' => $result,

        ];
    }
}
