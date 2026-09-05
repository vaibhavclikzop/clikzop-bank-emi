<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankService
{
    public function verify(string $account_no, string $ifsc_code)
    {
        try {

            $url = config('services.perfios.base_url').'/ssp/kyc/api/v3/bankacc-verification';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-id' => config('services.perfios.client_id'),
            ])->post($url, [
                'ifsc' => $ifsc_code,
                'accountNumber' => $account_no,
                'additionalDetails' => true,
                'consent' => 'Y',
            ]);

            $data = $response->json();

            if (! $response->successful()) {
                return response()->json([

                    'status' => false,
                    'message' => 'API request failed',
                    'data' => $data,
                ], 500);
            }
            $statusCode = $data['statusCode'] ?? null;

            if ($statusCode == 104) {

                return [
                    'status' => false,
                    'message' => 'Invalid IFSC CODE or Account No. or no data found',
                    'data' => $data,
                ];
            }

            // $data = [
            //     'requestId'=>1212,
            //     'result' => [
            //         'data' => [
            //             'source' => [
            //                 [
            //                     'statusAsPerSource' => 'VALID',
            //                     'data' => [
            //                         'accountNumber' => '4045615292',
            //                         'ifsc' => 'SBIN0062130',
            //                         'accountName' => 'Mr. Vaibhav Kumar',
            //                         'bankResponse' => 'SUCCESSFUL TRANSACTION',
            //                         'bankTxnStatus' => true,
            //                         'bankRRN' => '617097190540',
            //                         'statusCode' => 'KC01',
            //                     ],
            //                     'isValid' => true,
            //                 ]
            //             ]
            //         ],
            //         'comparisionData' => [],
            //     ]
            // ];

            $result = [
                'isValid' => $data['result']['data']['source'][0]['isValid'] ?? false,
                'accountNumber' => $data['result']['data']['source'][0]['data']['accountNumber'] ?? null,
                'ifsc' => $data['result']['data']['source'][0]['data']['ifsc'] ?? null,
                'accountName' => $data['result']['data']['source'][0]['data']['accountName'] ?? null,
                'bankResponse' => $data['result']['data']['source'][0]['data']['bankResponse'] ?? null,
                'bankTxnStatus' => $data['result']['data']['source'][0]['data']['bankTxnStatus'] ?? null,
                'bankRRN' => $data['result']['data']['source'][0]['data']['bankRRN'] ?? null,
                'statusCode' => $data['result']['data']['source'][0]['data']['statusCode'] ?? null,
                'requestId' => $data['requestId'],
            ];

            return [

                'status' => true,
                'message' => 'Verified successfully',
                'data' => $result,
                'apiResponse' => $data,
            ];
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
