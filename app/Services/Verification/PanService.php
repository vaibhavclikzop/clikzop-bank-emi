<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;

class PanService
{
    public function verify($pan)
    {

        if (! preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
            throw new \Exception('Invalid PAN format');
        }
        $data = null;
        $testingDocument = [
            "DFNPM5507K" => [
                "name" => "ADESH MANOCHA",
                "gender" => "male",
                "dob" => "1992-08-03",
                "status" => "Active",
                "city" => "Panchkula",
                "district" => "NA",
                "state" => "HARYANA",
                "country" => "",
                "pinCode" => "134113",
                "address" => "447 SECTOR-16 PANCHKULA Sector 15 Panchkula S.O"
            ],
            "KHFPS7261P" => [
                "name" => "GOURAV SAINI",
                "gender" => "male",
                "dob" => "2000-06-01",
                "status" => "Active",
                "city" => "",
                "district" => "NA",
                "state" => "",
                "country" => "India",
                "pinCode" => "",
                "address" => "  "
            ]
        ];

        if (isset($testingDocument[$pan])) {
            $data = $testingDocument[$pan];
        }
        if ($data) {

            $result = $data;
        } else {


            // return [
            //     'status' => false,
            //     'message' => 'Please use testing pan cards. [DFNPM5507K, KHFPS7261P]',
            //     'data' => $data,
            // ];

            $url = config('services.perfios.base_url') . '/ssp/kyc/api/v3/pan-profile-detailed';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-id' => config('services.perfios.client_id'),
            ])->post($url, [
                'pan' => $pan,
                'consent' => 'Y',
                'reason' => 'Customer onboarding',
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
                    'message' => 'Invalid PAN or no data found',
                    'data' => $data,
                ];
            }

            $result['name'] = $data['result']['name'];
            $result['gender'] = $data['result']['gender'];
            $result['dob'] = $data['result']['dob'];
            $result['status'] = $data['result']['status'];
            $result['city'] = $data['result']['address']['city'] ?? 'NA';
            $result['district'] = $data['result']['address']['district'] ?? 'NA';
            $result['state'] = $data['result']['address']['state'] ?? 'NA';
            $result['country'] = $data['result']['address']['country'] ?? 'NA';
            $result['pinCode'] = $data['result']['address']['pinCode'] ?? 'NA';
            $result['address'] = $data['result']['address']['buildingName'] . ' ' . $data['result']['address']['locality'] . ' ' . $data['result']['address']['streetName'];
        }






        return [
            'status' => true,
            'message' => 'PAN verified successfully',
            'data' => $result,
        ];
    }
}
