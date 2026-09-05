<?php

namespace App\Services\Verification;

use App\Models\Customers;
use Illuminate\Support\Facades\Http;

class AadhaarService
{
    public function verify($aadhar, $customer_id, $lat, $long)
    {

        try {

            $Customers = Customers::where('id', $customer_id)->first();
            if (!$Customers) {
                return [
                    'status' => false,
                    'message' => 'Customer not found',
                    'data' => [],
                ];
            }

            $data = null;
            $testingDocument = [
                "942381911351" => [
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
                "942381911352" => [
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

            if (isset($testingDocument[$aadhar])) {
                $data = $testingDocument[$aadhar];
            }
            if ($data) {

                $result = $data;
            } else {

                // return [

                //     'status' => false,
                //        'message' => 'Please use testing aadhar cards. [942381911351, 942381911352]',
                //     'data' => [],

                // ];

                $url = config('services.perfios.base_url') . '/ssp/kyc/api/v3/aadhaar-consent';
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-secure-id' => config('services.perfios.user_name'),
                    'x-secure-cred' => config('services.perfios.client_password'),
                    'x-organization-id' => config('services.perfios.client_id'),
                ])->post($url, [
                    'lat' => $lat,
                    'long' => $long,
                    'ipAddress' => request()->ip(),
                    'userAgent' => request()->userAgent(),
                    'deviceId' => 'NA',
                    'deviceInfo' => '672691719882454',
                    'consent' => 'Y',
                    'name' => $Customers->name,
                    'consentTime' => (string) time(),
                    'consentText' => 'Customer Testing',
                    'clientData' => [
                        'caseId' => (string) $customer_id,
                    ],
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

                if ($statusCode == 104) {

                    return [
                        'status' => false,
                        'message' => 'No data found',
                        'data' => $data,
                    ];
                }

                if ($statusCode == 101) {

                    $url1 = config('services.perfios.base_url') . '/ssp/kyc/api/v2/aadhaar-verification';
                    $response1 = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'x-secure-id' => config('services.perfios.user_name'),
                        'x-secure-cred' => config('services.perfios.client_password'),
                        'x-organization-id' => config('services.perfios.client_id'),
                    ])->post($url1, [
                        'aadhaarNo' => $aadhar,
                        'consent' => 'Y',
                        'checkValidation' => true,
                        'accessKey' => $data['result']['accessKey'],
                        'clientData' => [
                            'caseId' => (string) $customer_id,
                        ],
                    ]);

                    $aadharData = $response1->json();
                }

                $status = $aadharData['status-code'] ?? null;

                if ($status != 101) {
                    return [

                        'status' => false,
                        'message' => 'Invalid aadhar no.',
                        'data' => $aadharData,

                    ];
                }

                $result['name'] = 'NA';
                $result['gender'] = $aadharData['result']['gender'] ?? 'NA';

                $result['dob'] = 'NA';
                $result['status'] = 'NA';
                $result['city'] = 'NA';
                $result['district'] = 'NA';
                $result['state'] = $aadharData['result']['state'] ?? 'NA';
                $result['country'] = 'NA';
                $result['pinCode'] = 'NA';
                $result['address'] = 'NA';
            }
            return [

                'status' => true,
                'message' => 'Aadhar verified successfully',
                'data' => $result,

            ];
        } catch (\Throwable $th) {
            return [

                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ];
        }
    }
}
