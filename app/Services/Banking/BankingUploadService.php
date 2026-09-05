<?php

namespace App\Services\Banking;

use App\Models\Loan;
use App\Models\loan_banking_transactions;
use App\Models\loanBankingDet;
use Illuminate\Support\Facades\Http;

class BankingUploadService
{
    public function initiate($loanID, $instituteID)
    {

        $loan = Loan::with("loanType")->where("id", $loanID)->first();

        // $url = config('services.perfios.base_url') . '/ssp/insights/api/v3/transactions';
        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        //     'x-secure-id' => config('services.perfios.user_name'),
        //     'x-secure-cred' => config('services.perfios.client_password'),
        //     'x-organization-id' => config('services.perfios.client_id'),
        // ])->post($url, [
        //     'loanAmount' => $loan->loan_amount,
        //     'loanDuration' => $loan->tenure_months,
        //     'loanType' => $loan->loanType->name ?? "NA",
        //     'processingType' => "STATEMENT",
        //     'txnId' => $loan->loan_number,
        //     'acceptancePolicy' => 'atLeastOneTransactionInRange',
        //     'institutionId' => $instituteID,
        //     'uploadingScannedStatements' => false,
        //     'yearMonthFrom' => "",
        //     'yearMonthTo' => "",

        // ]);

        // $data = $response->json();

        // if (!$response->successful()) {
        //     return [

        //         'status' => false,
        //         'message' => 'API request failed',
        //         'data' => $data,
        //     ];
        // }
        $data =  [
            "transaction" => [
                "perfiosTransactionId" => "KATR1788435745771"
            ]
        ];

        if (isset($data["error"]) && !empty($data["error"])) {
            return [
                'status' => false,
                'message' => $data["error"]["message"] ?? 'Something went wrong',
                'data' => $data,
            ];
        } else {
            return [
                'status' => true,
                'message' => "Initiate Successfully",
                'transactionID' => $data["transaction"]["perfiosTransactionId"] ?? "NA",
                'data' => $data,
            ];
        }
    }
}
