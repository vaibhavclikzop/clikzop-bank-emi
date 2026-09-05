<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinService
{
    public function verify(string $cin_no)
    {
        try {

            $url = config('services.perfios.base_url').'/ssp/kscan/api/v3/corp/profile';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-ID' => config('services.perfios.client_id'),
            ])->post($url, [
                'entityId' => $cin_no,
                'consent' => 'y',
                'alternateSource' => true,
            ]);

            $data = $response->json();

            // $data = [
            //     'requestId' => 'a72af0c6-8e7c-4952-852d-509bd217eb0d',
            //     'result' => [
            //         'alternateSourceData' => false,
            //         'company' => [
            //             'whetherListedOrNot' => 'Unlisted',
            //             'category' => 'Company limited by shares',
            //             'registeredAddress' => 'OFFICE SPACE NO. 307, BLOCK D & E, CHANDIGARH CITI CENTRE, VIP ROAD, ZIRAKPUR, Mohali, Punjab, India, 140603',
            //             'rocCode' => 'ROC Chandigarh',
            //             'subcategory' => 'Non-government company',
            //             'emailId' => 'MANOCHA.ADESH3@GMAIL.COM',
            //             'suspendedAtStockExchange' => null,
            //             'dateOfBalanceSheet' => '31-03-2025',
            //             'cin' => 'U72200PB2022PTC055508',
            //             'alternativeAddress' => null,
            //             'status' => 'Active',
            //             'dateOfLastAGM' => '30-09-2025',
            //             'entityClass' => 'Private',
            //             'entityName' => 'CLIKZOP INNOVATIONS PRIVATE LIMITED',
            //             'paidUpCapital' => '200000',
            //             'authorisedCapital' => '1500000',
            //             'numberOfMembers' => null,
            //             'registrationNumber' => '055508',
            //             'dateOfIncorporation' => '25-03-2022',
            //             'industry' => 'Real estate, renting and business activities',
            //             'subIndustry' => 'Computer and related activities',
            //             'activityGroup' => 'Software publishing, consultancy and supply',
            //             'activityClass' => 'Software publishing, consultancy and supply',
            //             'activitySubClass' => 'Software publishing, consultancy and supply',
            //         ],
            //         'directors' => [
            //             [
            //                 'din' => '09548414',
            //                 'pan' => '*****6376K',
            //                 'dob' => '02-04-1964',
            //                 'fatherName' => '**** ***',
            //                 'name' => 'ANAND PARKASH',
            //                 'designation' => 'Director',
            //                 'tenureBeginDate' => '25-03-2022',
            //                 'tenureEndDate' => null,
            //                 'address' => 'SECTOR-16, PANCHKULA, Haryana, India, 134113',
            //             ],
            //             [
            //                 'din' => '11585982',
            //                 'pan' => '*****0809Q',
            //                 'dob' => '05-06-1987',
            //                 'fatherName' => '***** *****AN *****A',
            //                 'name' => 'BAHUDUTT SHARMA',
            //                 'designation' => 'Additional Director',
            //                 'tenureBeginDate' => '24-02-2026',
            //                 'tenureEndDate' => null,
            //                 'address' => 'Chandigarh, India, 160047',
            //             ],
            //         ],
            //         'charges' => [],
            //     ],
            //     'statusCode' => 101,
            // ];

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
                    'message' => 'Invalid IFSC CODE or Account No. or no data found',
                    'data' => $data,
                ];
            }

            if ($data['statusCode'] == 101) {
                $url1 = config('services.perfios.base_url').'/ssp/kscan/api/v3/cin-pan';

                $response1 = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-secure-id' => config('services.perfios.user_name'),
                    'x-secure-cred' => config('services.perfios.client_password'),
                    'x-organization-ID' => config('services.perfios.client_id'),
                ])->post($url1, [
                    'cin' => $cin_no,
                    'consent' => 'y',
                    'alternateSource' => true,
                ]);
                $pan = null;
                if (($response1['statusCode'] ?? 0) == 101) {
                    $pan = $response1['result'][0]['pans'][0] ?? null;
                }

                $data['pan_no'] = $pan;

                return [
                    'status' => true,
                    'message' => 'Verified successfully',
                    'data' => $data,
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'data' => $data,
                ];
            }
        } catch (\Throwable $e) {

            Log::error('Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
}
