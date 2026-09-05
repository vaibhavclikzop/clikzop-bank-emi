<?php

namespace App\Services\CIBILService;

use App\Models\Loan;
use Illuminate\Support\Facades\Http;

class CibilService
{
    public function getDetails($mobile_number, $pan_no, $full_name)
    {




        //     $response = [
        //         'data' => [
        //             'currentCredit' => 15070,
        //             'creditUsed' => 70,
        //             'result' => [
        //                 'reportUrl' => 'https://objectstorage.ap-mumbai-1.oraclecloud.com/...',
        //                 'webUrl' => 'https://myscore.cibil.com/...',
        //                 'data' => [
        //                     'GetCustomerAssetsResponse' => [
        //                         'ResponseStatus' => 'Success',
        //                         'ResponseKey' => 'b6838c44d3a10429:7ae7c575:19ffed2f74a:-92',

        //                         'GetCustomerAssetsSuccess' => [
        //                             'CreditSummaryData' => [
        //                                 'OldestCreditAccountPeriod' => '186',
        //                                 'Inquires' => '24',
        //                                 'OnTimePaymentHistory' => '99.75',
        //                                 'CreditCardUtilization' => '1',
        //                                 'CreditMix' => '60',
        //                             ],

        //                             'Asset' => [
        //                                 'Status' => 'Active',
        //                                 'SafetyCheckFailure' => false,
        //                                 'ExpirationDate' => '2028-08-13T11:35:29.192+05:30',
        //                                 'CreationDate' => '2026-08-14T11:35:29.192+05:30',

        //                                 'TrueLinkCreditReport' => [
        //                                     'ReferenceKey' => '11468757760',
        //                                     'currentversion' => '5.0',

        //                                     'Borrower' => [
        //                                         'borrowerKey' => '463775028',

        //                                         'Birth' => [
        //                                             'date' => '1982-01-10+05:30',
        //                                             'partitionSet' => '0',

        //                                             'BirthDate' => [
        //                                                 'month' => '1',
        //                                                 'year' => '1982',
        //                                                 'day' => '10',
        //                                             ],

        //                                             'age' => '0',
        //                                         ],

        //                                         'CreditScore' => [
        //                                             'riskScore' => '743',
        //                                             'populationRank' => '25',
        //                                             'scoreName' => 'CIBILTransUnionScore3',

        //                                             'NoScoreReason' => [
        //                                                 'symbol' => '',
        //                                                 'description' => '',
        //                                                 'rank' => '100000',
        //                                                 'abbreviation' => '',
        //                                             ],

        //                                             'Source' => [
        //                                                 'Reference' => 'd9f12d1f-3ac0-40f8-b31e-8f8829fe136b',
        //                                                 'InquiryDate' => '2026-08-14+05:30',
        //                                                 'Locale' => 'en_IN',
        //                                                 'BorrowerKey' => '463775028',

        //                                                 'Bureau' => [
        //                                                     'symbol' => 'CIBIL',
        //                                                     'description' => '',
        //                                                     'rank' => '100000',
        //                                                     'abbreviation' => '',
        //                                                 ],
        //                                             ],
        //                                         ],

        //                                         'Gender' => 'Male',

        //                                         'Employer' => [
        //                                             'serialNumber' => '4146048187',
        //                                             'IncomeFreqIndicator' => '',
        //                                             'NetGrossIndicator' => '',
        //                                             'dateReported' => '2026-08-09+05:30',
        //                                             'name' => '',

        //                                             'OccupationCode' => [
        //                                                 'symbol' => '04',
        //                                                 'description' => 'Others',
        //                                                 'rank' => '100000',
        //                                                 'abbreviation' => '',
        //                                             ],

        //                                             'partitionSet' => '0',
        //                                         ],

        //                                         'EmailAddress' => [
        //                                             [
        //                                                 'serialNumber' => '565409601',
        //                                                 'Email' => 'BANKSGURSIMRAN@GMAIL.COM',
        //                                             ],
        //                                             [
        //                                                 'serialNumber' => '434564945',
        //                                                 'Email' => 'CEEDEEDEVELOPERSPVT.LTD@GMAIL.COM',
        //                                             ],
        //                                         ],

        //                                         'PhoneNumber' => [
        //                                             [
        //                                                 'SerialNumber' => '2494500765',
        //                                                 'Number' => '9875998362',
        //                                                 'enrichMode' => 'R',

        //                                                 'PhoneType' => [
        //                                                     'symbol' => '03',
        //                                                     'description' => '',
        //                                                     'rank' => '100000',
        //                                                     'abbreviation' => '',
        //                                                 ],
        //                                             ],
        //                                         ],

        //                                         // Account / Tradeline data
        //                                         'Account' => [
        //                                             [
        //                                                 'accountTypeAbbreviation' => '',
        //                                                 'accountTypeDescription' => '',
        //                                                 'accountTypeSymbol' => '12',

        //                                                 'Tradeline' => [
        //                                                     'creditorName' => 'HDFC BANK',
        //                                                     'branch' => '',
        //                                                     'highBalance' => '2000000',
        //                                                     'dateOpened' => '2025-08-29+05:30',
        //                                                     'dateReported' => '2026-08-05+05:30',
        //                                                     'currentBalance' => '-13252',
        //                                                     'subscriberCode' => '3080001',
        //                                                     'accountNumber' => '50200083037536',

        //                                                     'AccountDesignator' => [
        //                                                         'symbol' => '1',
        //                                                         'description' => '',
        //                                                         'rank' => '100000',
        //                                                         'abbreviation' => '',
        //                                                     ],

        //                                                     'AccountCondition' => [
        //                                                         'symbol' => '',
        //                                                         'description' => '',
        //                                                         'rank' => '100000',
        //                                                         'abbreviation' => '',
        //                                                     ],

        //                                                     'PayStatus' => [
        //                                                         'symbol' => '',
        //                                                         'description' => '',
        //                                                         'rank' => '100000',
        //                                                         'abbreviation' => '',
        //                                                     ],

        //                                                     'writtenOffAmtTotal' => '-1',
        //                                                     'amountPastDue' => '0',
        //                                                     'settlementAmount' => '-1',
        //                                                     'writtenOffPrincipal' => '-1',
        //                                                 ],
        //                                             ],

        //                                             [
        //                                                 'accountTypeAbbreviation' => '',
        //                                                 'accountTypeDescription' => '',
        //                                                 'accountTypeSymbol' => '01',

        //                                                 'Tradeline' => [
        //                                                     'creditorName' => 'ICICI BANK',
        //                                                     'highBalance' => '7500000',
        //                                                     'dateOpened' => '2024-02-02+05:30',
        //                                                     'dateReported' => '2026-07-31+05:30',
        //                                                     'currentBalance' => '5424271',
        //                                                     'subscriberCode' => '3090001',
        //                                                     'accountNumber' => 'LACHD00049422892',

        //                                                     'AccountDesignator' => [
        //                                                         'symbol' => '4',
        //                                                         'description' => '',
        //                                                         'rank' => '100000',
        //                                                         'abbreviation' => '',
        //                                                     ],

        //                                                     'dateAccountStatus' => '2026-07-10+05:30',

        //                                                     'GrantedTrade' => [
        //                                                         'interestRate' => '-1.00',

        //                                                         'PaymentFrequency' => [
        //                                                             'symbol' => '03',
        //                                                             'description' => '',
        //                                                             'rank' => '100000',
        //                                                             'abbreviation' => '',
        //                                                         ],
        //                                                     ],
        //                                                 ],
        //                                             ],
        //                                         ],
        //                                     ],
        //                                 ],
        //                             ],
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],

        //         'billableType' => 'FULL',
        //         'refId' => 'AL6a7eb02d7de2e42c5c010b5e',
        //         'error' => false,
        //         'requestId' => '6a7eb0287de2e42c5c010b5d',
        //         'apiName' => 'USER_CIBIL_REPORT',
        //         'dateTime' => '2026-08-14T06:05:36.314Z',
        //     ];
        //   $data = $response;


        $url = 'https://bifrost.unifers.ai/enrich/get-cibil-report';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization'  => "rk_2aaf29db927a6bea2eea216d75a4ebb7b63bd43c6ff12013856822e3b965f861",
        ])->post($url, [
            'Mobile_Number'       => $mobile_number,
            'PAN_Number'          => $pan_no,
            'Full_Name'          =>  $full_name,
            'Callback_Url'        => 'https://backend-bankemi.clikzopdevp.com/api/webhook/karza-webhook?token=79fb0fed215b6e8031b263285aa44999b2ec27e411db88ef41d422082999f262',
            'Concent_Text'    => "We confirm and undertake that valid end-user consent has been obtained for fetching CIBIL REPORT using MOBILE NUMBER, and that such consent remains active and unrevoked at the time of this request.",
            'Concent' => 'Y',
        ]);
        $data = $response->json();

        if (!$response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }


        if ($data["error"] == false) {
            return [
                'status' => true,
                'message' => "Fetch successfully",
                'request_id' => $data["requestId"],
                'data' => $data,
            ];
        }

        return [
            'status' => false,
            'message' => "Something went wrong",
            'request_id' => $data["requestId"],
            'data' => $data,
        ];
    }
}
