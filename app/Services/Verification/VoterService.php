<?php

namespace App\Services\Verification;

class VoterService
{
    public function verify($voter, $dob)
    {

        $result['name'] = 'NA';
        $result['gender'] = $data['result']['gender'] ?? 'NA';

        $result['dob'] = $data['result']['dob'] ?? 'NA';
        $result['status'] = $data['result']['status'] ?? 'NA';
        $result['city'] = $data['result']['address'][0]['city'] ?? 'NA';
        $result['district'] = $data['result']['address'][0]['district'] ?? 'NA';
        $result['state'] = $data['result']['address'][0]['state'] ?? 'NA';
        $result['country'] = $data['result']['address'][0]['country'] ?? 'NA';
        $result['pinCode'] = $data['result']['address'][0]['pin'] ?? 'NA';
        $result['address'] = ($data['result']['address'][0]['completeAddress'] ?? 'NA');

        return response()->json([

            'status' => true,
            'message' => 'Voter ID verified successfully',
            'data' => $result,

        ], 200);
    }
}
