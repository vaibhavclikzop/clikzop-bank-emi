<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\CibilScores;
use App\Services\CIBILService\CibilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CibilController extends Controller
{
    public function fetchCIBIL(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'pan_no' => 'required',
            'full_name' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }


        try {

            $response = app(CibilService::class)->getDetails($request->mobile_number, $request->pan_no, $request->full_name);

            if ($response["status"] == true) {

                $CreditSummaryData =   $response["data"]["data"]["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"];

                $CreditScore =   $response["data"]["data"]["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["Asset"]["TrueLinkCreditReport"]["Borrower"]["CreditScore"];


                // $BorrowerName =   $response["data"]["data"]["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["Asset"]["TrueLinkCreditReport"]["Borrower"]["BorrowerName"];
                CibilScores::updateOrCreate([
                    "mobile_number" => $request->mobile_number,
                    "pan_no" => $request->pan_no,
                ], [
                    "full_name" => $request->full_name,
                    "current_credit" => $response["data"]["data"]["currentCredit"],
                    "credit_used" => $response["data"]["data"]["creditUsed"],
                    "report_url" => $response["data"]["data"]["result"]["reportUrl"],
                    "web_url" => $response["data"]["data"]["result"]["webUrl"],
                    "response_key" => $response["data"]["data"]["result"]["data"]["GetCustomerAssetsResponse"]["ResponseKey"],
                    "Oldest_credit_account_period" => $CreditSummaryData["OldestCreditAccountPeriod"],
                    "inquires" => $CreditSummaryData["Inquires"],
                    "on_time_payment_history" => $CreditSummaryData["OnTimePaymentHistory"],
                    "credit_card_utilization" => $CreditSummaryData["CreditCardUtilization"],
                    "credit_mix" => $CreditSummaryData["CreditMix"],
                    "date" => date(now()),
                    "risk_score" => $CreditScore["riskScore"],
                    "population_rank" => $CreditScore["populationRank"],
                    "user_id" => $request->user()->id,
                    // "fore_name" => $BorrowerName["Name"]["Forename"],
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Save Successfully',
            'data' => $response["data"]["data"],
        ], 200);
    }

    public function getCibilScoreList(Request $request, $offset, $limit)
    {

        try {
            $totalRecords = CibilScores::count();
            $data = CibilScores::offset($offset)
                ->limit($limit)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,
                'pagination' => [
                    'total_records' => $totalRecords,
                    'offset' => (int) $offset,
                    'limit' => (int) $limit,
                    'current_page' => floor($offset / $limit) + 1,
                    'total_pages' => ceil($totalRecords / $limit),
                    'has_more' => ($offset + $limit) < $totalRecords,
                ],
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
