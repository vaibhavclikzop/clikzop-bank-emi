<?php

namespace App\Services\Verification;

use App\Models\Bank;
use App\Models\IfscMaster;
use Illuminate\Support\Facades\Http;

class IfscCodeService
{
    public function verify($ifsc)
    {
        $ifsc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $ifsc));

        $url = config('services.perfios.base_url').'/ssp/kyc/api/v2/ifsc';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-secure-id' => config('services.perfios.user_name'),
            'x-secure-cred' => config('services.perfios.client_password'),
            'x-organization-id' => config('services.perfios.client_id'),
        ])->post($url, [
            'ifsc' => $ifsc,

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
        $statusCode = $data['status-code'] ?? null;

        if ($statusCode == 102) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid IFSC CODE or no data found',
                'data' => $data,
            ], 422);
        }
        // $data = [
        //     'result' => [
        //         'city' => 'MOHALI, PUNJAB',
        //         'district' => 'MOHALI, PUNJAB',
        //         'ifsc' => 'PUNH0352900',
        //         'micr' => '160024014',
        //         'state' => 'PUNJAB',
        //         'contact' => null,
        //         'branch' => 'MOHALI PHASE - VII,DISTTROPAR',
        //         'address' => 'PHASE-VII,',
        //         'bank' => 'PUNJAB NATIONAL BANK',
        //         'office' => 'MOHALI PHASE - VII,DISTTROPAR',
        //     ]
        // ];

        $result['city'] = $data['result']['city'];
        $result['district'] = $data['result']['district'];
        $result['ifsc'] = $data['result']['ifsc'];
        $result['micr'] = $data['result']['micr'];
        $result['state'] = $data['result']['state'];
        $result['contact'] = $data['result']['contact'];
        $result['branch'] = $data['result']['branch'];
        $result['address'] = $data['result']['address'];
        $result['name'] = $data['result']['bank'];
        $result['office'] = $data['result']['office'];
        $bankCode = substr($data['result']['ifsc'], 0, 4);

        $bank = Bank::where('bank_code', $bankCode)->first();
        if (! $bank) {
            $bank = Bank::create([
                'name' => $result['name'],
                'ifsc_code' => 'NA',
                'branch_name' => 'NA',
                'bank_code' => $bankCode,
                'logo_url' => 'NA',
                'website' => 'NA',
                'category' => 'NA',
                'active' => 'active',

            ]);
        }

        $master = IfscMaster::create([
            'city' => $data['result']['city'] ?? null,
            'district' => $data['result']['district'] ?? null,
            'ifsc' => $data['result']['ifsc'] ?? null,
            'micr' => $data['result']['micr'] ?? null,
            'state' => $data['result']['state'] ?? null,
            'contact' => $data['result']['contact'] ?? null,
            'branch' => $data['result']['branch'] ?? null,
            'address' => $data['result']['address'] ?? null,
            'name' => $data['result']['bank'] ?? null,
            'office' => $data['result']['office'] ?? null,
            'bank_code' => $bankCode,
            'bank_id' => $bank->id,
        ]);

        return response()->json([

            'status' => true,
            'message' => 'IFSC Code verified successfully',
            'data' => $master,

        ], 200);
    }
}
