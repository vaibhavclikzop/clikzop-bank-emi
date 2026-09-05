<?php

namespace App\Services\Setu;

use App\Models\LoanBankingConsent;
use Illuminate\Support\Facades\Http;

class SetuService
{

    public function generateToken()
    {

        $url = "https://orgservice-prod.setu.co/v1/users/login";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-client_id'  => config('services.SETU.SETU_CLIENT_ID'),
            'x-client-secret'  => config('services.SETU.SETU_CLIENT_SECRET'),
            'x-product-instance-id'  => config('services.SETU.SETU_PRODUCT_INSTANCE_ID'),

            'client' => "bridge",
        ])->post($url, [
            "grant_type" => "client_credentials",
            'clientID'  => config('services.SETU.SETU_CLIENT_ID'),
            'secret'  => config('services.SETU.SETU_CLIENT_SECRET'),

        ]);
        $data = $response->json();


        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }
        if ($data["access_token"]) {
            return $data["access_token"];
        } else {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }
    }

    public function createConsent($loan_id, $number, $from_date, $to_date)
    {

        $token = $this->generateToken();

        $url = config('services.SETU.SETU_BASE_URL') . '/v2/consents';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-client_id' => config('services.SETU.SETU_CLIENT_ID'),
            'x-client-secret' => config('services.SETU.SETU_CLIENT_SECRET'),
            'x-product-instance-id' => config('services.SETU.SETU_PRODUCT_INSTANCE_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, [
            'consentDuration' => [
                'unit'  => 'MONTH',
                'value' => 24,
            ],
            'vua' => $number,
            'dataRange' => [
                'from' => $from_date . 'T00:00:00Z',
                'to'   => $to_date . 'T00:00:00Z',
            ],

            'context' => [],
        ]);
        $data = $response->json();


        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
                'token' => $token,
            ];
        }


        if ($data["status"]) {
            return [
                'status' => true,
                'message' => "Consent Create successfully",
                'data' => $data,
            ];
        }

        return [

            'status' => false,
            'message' => "Something went wrong",

            'data' => $data,
        ];
    }


    public function checkConsent($id, $consent_id)
    {

        $token = $this->generateToken();

        $url = config('services.SETU.SETU_BASE_URL') . '/v2/consents/' . $consent_id;

        $response = Http::withHeaders([
            'x-client_id' => config('services.SETU.SETU_CLIENT_ID'),
            'x-client-secret' => config('services.SETU.SETU_CLIENT_SECRET'),
            'x-product-instance-id' => config('services.SETU.SETU_PRODUCT_INSTANCE_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);
        $data = $response->json();


        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
                'token' => $token,
            ];
        }
        if ($data["status"]) {
            $session = [];
            if ($data["status"] == "ACTIVE") {
                $session =  $this->createSession($id, $consent_id, $token);
            }
            return [
                'status' => true,
                'message' => "Consent checked successfully",
                'data' => $data,
                'session' => $session,
            ];
        }

        return [

            'status' => false,
            'message' => "Something went wrong",

            'data' => $data,
        ];
    }

    public function createSession($id, $consent_id, $token)
    {

        $consent =  LoanBankingConsent::where("id", $id)->first();
        $url = config('services.SETU.SETU_BASE_URL') . '/v2/sessions';

        $response = Http::withHeaders([
            'x-client_id' => config('services.SETU.SETU_CLIENT_ID'),
            'x-client-secret' => config('services.SETU.SETU_CLIENT_SECRET'),
            'x-product-instance-id' => config('services.SETU.SETU_PRODUCT_INSTANCE_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, [
            'consentId' => $consent_id,
            'dataRange' => [
                'from' => $consent->from_date . 'T00:00:00Z',
                'to'   => $consent->to_date . 'T00:00:00Z',
            ],
            "format" => "json"
        ]);
        $data = $response->json();


        if (!$response->successful()) {

            return [
                'status' => false,
                'message' => 'API request failed while creating session',
                'data' => $data,
                'token' => $token,
            ];
        }

        if ($data["status"]) {
            return [
                'status' => true,
                'message' => "ID created successfully",
                'data' => $data,
            ];
        }

        return [

            'status' => false,
            'message' => "Something went wrong",
            'data' => $data,
        ];
    }

    public function getBankStatement($id, $consent_id)
    {
        $token = $this->generateToken();

        $url =   "https://fiu-sandbox.setu.co/v2/sessions/" . $consent_id;

        // $url =  'https://fiu-sandbox.setu.co/v2/sessions/f26478b1-4cda-4f02-9166-e461d144809c';
        $response = Http::withHeaders([
            'x-client_id' => config('services.SETU.SETU_CLIENT_ID'),
            'x-client-secret' => config('services.SETU.SETU_CLIENT_SECRET'),
            'x-product-instance-id' => config('services.SETU.SETU_PRODUCT_INSTANCE_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);
        $data = $response->json();
        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed while fetching statement',
                'data' => $data,

            ];
        }
        if ($data["status"] == "COMPLETE" || $data["status"] == "FAILED") {

            return [
                'status' => true,
                'message' => 'Fetch successfully',
                'data' => $data,

            ];
        } else if ($data["status"] == "PENDING") {
            return [
                'status' => false,
                'message' => 'Fetching details check after sometime',
                'data' => $data,

            ];
        } else {
            return [
                'status' => false,
                'message' => 'Something wrong',
                'data' => $data,

            ];
        }
    }
}
