<?php

namespace App\Services\Verification;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GSTDetailsByGSTINService
{
    public function verify(string $gst_no)
    {
        try {

            $url = config('services.perfios.base_url').'/ssp/kscan/api/v3/gst-profile';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-ID' => config('services.perfios.client_id'),
            ])->post($url, [
                'id' => $gst_no,
                'consent' => 'y',
                'alternateSource' => true,
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
                    'message' => 'Invalid DATA  or no data found',
                    'data' => $data,
                ];
            }

            if ($data['statusCode'] == 101) {

                $result['masterData'] = [
                    'company_name' => $response['result'][0]['legalName'] ?? null,
                    'email_id' => $response['result'][0]['email'] ?? null,
                    'gst_in' => $response['result'][0]['gstin'] ?? null,
                    'gst_in_ref' => null,
                    'mobile' => $response['result'][0]['contact'] ?? null,
                    'pan' => $response['result'][0]['pan'] ?? null,
                    'registration_name' => $response['result'][0]['legalName'] ?? null,
                    'tin_number' => $response['result'][0]['tin'] ?? null,
                    'state' => $response['result'][0]['state_'] ?? null,
                    'stjCd' => null,
                    'dty' => $response['result'][0]['dealerType'] ?? null,
                    'stj' => $response['result'][0]['stateJurisdiction'] ?? null,
                    'nba' => ! empty($response['result'][0]['natureOfBusiness'])
                        ? implode(', ', $response['result'][0]['natureOfBusiness'])
                        : null,
                    'ctb' => $response['result'][0]['constitutionOfBusiness'] ?? null,
                    'registration_date' => ! empty($response['result'][0]['dateOfRegistration'])
                        ? Carbon::parse($response['result'][0]['dateOfRegistration'])->format('d/m/Y')
                        : null,
                    'address' => $response['result'][0]['address'] ?? null,
                    'trade_name' => $response['result'][0]['tradeName'] ?? null,
                    'ctjCd' => null,
                    'status' => $response['result'][0]['status'] ?? null,
                    'ctj' => $response['result'][0]['centreJurisdiction'] ?? null,
                    'e_invoice_status' => $response['result'][0]['einvoiceStatus'] ?? null,
                ];

                $url1 = config('services.perfios.base_url').'/ssp/gst/api/v2/gst-return-status';

                $response1 = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-secure-id' => config('services.perfios.user_name'),
                    'x-secure-cred' => config('services.perfios.client_password'),
                    'x-organization-ID' => config('services.perfios.client_id'),
                ])->post($url1, [
                    'gstin' => $response['result'][0]['gstin'],
                    'consent' => 'y',
                    'alternateSource' => true,
                    'liabilityDetails' => false,
                    'dueInfo' => true,
                    'latestFy' => false,
                ]);

                foreach ($response1['result']['result'] as $yearData) {

                    foreach ($yearData['eFiledlist'] as $item) {

                        $result['fillingData'][] = [
                            'valid' => $item['valid'] ?? null,
                            'mof' => $item['mof'] ?? null,
                            'dof' => $item['dof'] ?? null,
                            'return_type' => $item['rtntype'] ?? null,
                            'return_period' => $item['retPrd'] ?? null,
                            'arn' => $item['arn'] ?? null,
                            'status' => $item['status'] ?? null,
                            'due_date' => $item['dueDt'] ?? null,
                            'is_delay' => $item['isDelay'] ?? false,
                            'delay_days' => $item['delayDays'] ?? null,
                        ];
                    }
                }

                return [
                    'status' => true,
                    'message' => 'Verified successfully',
                    'data' => $result,
                    'apiResponse' => $data,
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
