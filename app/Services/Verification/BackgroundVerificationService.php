<?php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BackgroundVerificationService
{
    public function verify(string $name,  $dob)
    {
        try {

            $data["result"] = "Test Response";
            return [
                'status' => true,
                'message' => 'Verified successfully',
                'data' => $data,
                "statusCode" => 101
            ];

            $url = config('services.perfios.base_url') . '/ssp/kscan/api/v1/bgv-data';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-secure-id' => config('services.perfios.user_name'),
                'x-secure-cred' => config('services.perfios.client_password'),
                'x-organization-ID' => config('services.perfios.client_id'),
            ])->post($url, [
                "candidateName" => $name,
                "alternateName" => null,
                "relativeName" => null,
                "address" => null,
                "alternateAddress" => null,
                "fuzziness" => false,
                "fuzzinessLevel" => "",
                "stateCode" => [],
                "district" => "",
                "countOnly" => false,
                "entityRelation" => "b",
                "dob" => "",
                "addressSplit" => false,
                "courtTypes" => [
                    "districtCourts" => [],
                    "tribunalCourts" => [],
                    "highCourts" => [],
                    "supremeCourt" => [],
                    "consumerCourt" => [],
                    "reraCourts" => [],
                ],
                "advancedCourtFilter" => "",
                "civilCriminal" => "",
                "caseAct" => "",
                "caseSection" => "",
                "caseStatus" => "",
                "advanceSearchParty" => false,
                "firdata" => true,
                "confidenceLevel" => ["high", "medium", "low"]
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

            if ($statusCode == 102) {

                return [
                    'status' => true,
                    'message' => 'No Data Found',
                    'data' => $data,
                    'statusCode' => 102,
                ];
            }

            if ($statusCode != 101) {

                return [
                    'status' => false,
                    'message' => 'Invalid data or no data found',
                    'data' => $data,
                    "statusCode" => $statusCode
                ];
            }

            if ($data['statusCode'] == 101) {



                return [
                    'status' => true,
                    'message' => 'Verified successfully',
                    'data' => $data,
                    "statusCode" => $statusCode
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'data' => $data,
                    "statusCode" => $statusCode
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
