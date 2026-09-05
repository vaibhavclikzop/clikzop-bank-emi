<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GSTDetailsService
{
    public function verify(string $pan)
    {
        try {

            $url = config('services.perfios.base_url').'/ssp/gst/api/v2/gst-advanced';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-ID' => config('services.perfios.client_id'),
            ])->post($url, [
                'pan' => $pan,
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
                    'message' => 'Invalid PAN no or no data found',
                    'data' => $data,
                ];
            }

            if ($data['statusCode'] == 101) {

                $result['masterData'] = [
                    'company_name' => $data['result'][0]['profile']['lgnm'],
                    'email_id' => $data['result'][0]['emailId'],
                    'gst_in' => $data['result'][0]['gstinId'],
                    'gst_in_ref' => $data['result'][0]['gstinRefId'],
                    'mobile' => $data['result'][0]['mobNum'],
                    'pan' => $data['result'][0]['pan'],
                    'registration_name' => $data['result'][0]['registrationName'],
                    'tin_number' => $data['result'][0]['tinNumber'],
                    'state' => $data['result'][0]['state'],
                    'stjCd' => $data['result'][0]['profile']['stjCd'],
                    'dty' => $data['result'][0]['profile']['dty'],
                    'stj' => $data['result'][0]['profile']['stj'],
                    'nba' => $data['result'][0]['profile']['nba'][0],
                    'ctb' => $data['result'][0]['profile']['ctb'],
                    'registration_date' => $data['result'][0]['profile']['rgdt'],
                    'address' => $data['result'][0]['profile']['pradr']['adr'],
                    'trade_name' => $data['result'][0]['profile']['tradeNam'],
                    'ctjCd' => $data['result'][0]['profile']['ctjCd'],
                    'status' => $data['result'][0]['profile']['sts'],
                    'ctj' => $data['result'][0]['profile']['ctj'],
                    'e_invoice_status' => $data['result'][0]['profile']['einvoiceStatus'],
                ];
                $fillingDetails = $data['result'][0]['filingStatus']['result'];
                foreach ($fillingDetails as $item) {
                    foreach ($item['eFiledlist'] as $filed) {

                        $result['fillingData'][] = [

                            'valid' => $filed['valid'] ?? null,
                            'mof' => $filed['mof'] ?? null,
                            'dof' => $filed['dof'] ?? null,
                            'return_type' => $filed['rtntype'] ?? null,
                            'return_period' => $filed['retPrd'] ?? null,
                            'arn' => $filed['arn'] ?? null,
                            'status' => $filed['status'] ?? null,
                            'filed_date' => $filed['dof'] ?? null,
                            'due_date' => $filed['dueDt'] ?? null,
                            'is_delay' => $filed['isDelay'] ?? false,
                            'delay_days' => $filed['delayDays'] ?? 0,
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
