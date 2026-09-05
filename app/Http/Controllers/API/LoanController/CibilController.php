<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanCibilAccounts;
use App\Models\LoanCibilDataMst;
use App\Models\LoanCibilEnquiries;
use App\Services\CIBILService\CibilService;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Validator;

class CibilController extends Controller
{
    public function uploadCibilPDF(Request $request)
    {


        $validator = Validator::make($request->all(), [
            "loan_id" => "required",
            'pdf' => 'required|mimes:pdf|max:20480',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $Loan =  Loan::where("id", $request->loan_id)->first();
        if (!$Loan) {
            return response()->json([
                'status' => false,
                'message' => "Loan ID not found",
                'data' => [],
            ], 404);
        }
        try {

            $parser = new Parser();
            $pdf = $parser->parseFile($request->file('pdf')->getRealPath());
            $text = $pdf->getText();

            $file = $request->file('pdf');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('cibil', $fileName, 'public');

            $text = preg_replace('/Page\s+\d+\s+of\s+\d+/i', '', $text);
            $text = preg_replace('/https?:\/\/\S+/', '', $text);
            $text = preg_replace('/\n{2,}/', "\n", $text);
            $text = preg_replace('/\d{2}\/\d{2}\/\d{4},\s*\d{2}:\d{2}\s*CIBIL Report/i', '', $text);
            $text = preg_replace('/\t+\d+\/\d+/', '', $text);
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
            $text = trim($text);
            $textLength = mb_strlen($text);

            LoanCibilDataMst::updateOrCreate(
                [
                    "loan_id" => $request->loan_id,
                    "customer_id" => $Loan->customer_id,
                ],
                [

                    "file" => $filePath,
                    "status" => "pending",
                    "raw_data" => $text,
                    "user_id" => $request->user()->id,

                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'PDF text extracted successfully.',
                'data' => [
                    'text' => $text,
                    'textLength' => $textLength,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getCibilData(Request $request)
    {
        //Mobile_Number, PAN_Number, Full_Name, ,Callback_Url ,Concent_Text , Concent

        $validator = Validator::make($request->all(), [
            "loan_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        try {

            $Loan =   Loan::with("customer")->where("id", $request->loan_id)->first();
            if (!$Loan) {
                return response()->json([
                    'status' => false,
                    'message' => "Loan ID not found",
                    'data' => [],
                ], 404);
            }

            $mobile_number = $Loan->customer->number;
            $pan_no = $Loan->customer->pan_no;
            $full_name = $Loan->customer->name;

 
            $response =  app(CibilService::class)->getDetails($mobile_number, $pan_no, $full_name);


            // $path = base_path('webhook_files/' . "cibil.json");
            // $response = json_decode(file_get_contents($path), true);

            $data = $response["data"]["data"];
            $Borrower = $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["Asset"]["TrueLinkCreditReport"]["Borrower"];
            $accountDetails = $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["Asset"]["TrueLinkCreditReport"]["TradeLinePartition"];

            $InquiryPartition = $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["Asset"]["TrueLinkCreditReport"]["InquiryPartition"];


            if ($response["status"] == true) {


                $loan_cibil_mst_id =   LoanCibilDataMst::updateOrCreate(
                    [
                        "loan_id" => $Loan->id
                    ],
                    [
                        "customer_id" => $Loan->customer_id,
                        "request_id" => $response["request_id"],
                        "currentCredit" => $data["currentCredit"],
                        "creditUsed" => $data["creditUsed"],
                        "reportUrl" =>  $response["data"]["data"]["result"]["reportUrl"],
                        "OldestCreditAccountPeriod" =>  $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"]["OldestCreditAccountPeriod"],
                        "Inquires" =>  $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"]["Inquires"],
                        "OnTimePaymentHistory" =>  $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"]["OnTimePaymentHistory"],
                        "CreditCardUtilization" =>  $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"]["CreditCardUtilization"],
                        "CreditMix" =>  $data["result"]["data"]["GetCustomerAssetsResponse"]["GetCustomerAssetsSuccess"]["CreditSummaryData"]["CreditMix"],
                        "serialNumber" => $Borrower["Employer"]["serialNumber"],
                        "cibil_score" => $Borrower["CreditScore"]["riskScore"],
                        "date_of_birth" => $Borrower["Birth"]["date"],
                        "user_id" => $request->user()->id,
                    ]
                );
                foreach ($accountDetails as $key => $value) {

                    LoanCibilAccounts::updateOrCreate(
                        [
                            "loan_cibil_mst_id" => $loan_cibil_mst_id->id,
                            "account_number" =>  $value["Tradeline"]["accountNumber"],

                        ],
                        [
                            "member_name" => $value["Tradeline"]["creditorName"] ?? null,
                            "sanctioned_amount" => $value["Tradeline"]["highBalance"] ?? null,
                            "date_opened" => $value["Tradeline"]["dateOpened"] ?? null,
                            "date_reported" => $value["Tradeline"]["dateReported"] ?? null,
                            "date_closed" => $value["Tradeline"]["dateClosed"] ?? null,
                            "current_balance" => $value["Tradeline"]["currentBalance"] ?? null,
                            "emi_amount" => $value["Tradeline"]["GrantedTrade"]["EMIAmount"] ?? null,
                            "repayment_tenure" => $value["Tradeline"]["GrantedTrade"]["termMonths"] ?? null,
                            "payment_history" => json_encode($value["Tradeline"]["GrantedTrade"]["PayStatusHistory"]) ?? null,
                            "cash_limit" => $value["Tradeline"]["GrantedTrade"]["CashLimit"] ?? null,
                            "credit_limit" => $value["Tradeline"]["GrantedTrade"]["CreditLimit"] ?? null,
                            "rate_of_interest" => $value["Tradeline"]["GrantedTrade"]["interestRate"] ?? null,
                            "account_status" => !empty(data_get($value, "Tradeline.dateClosed")) ? "closed" : "open",
                        ]
                    );
                }
                LoanCibilEnquiries::where("loan_cibil_mst_id", $loan_cibil_mst_id->id)->delete();
                foreach ($InquiryPartition as $key => $value) {

                    LoanCibilEnquiries::updateOrCreate([
                        "loan_cibil_mst_id" => $loan_cibil_mst_id->id,
                        "member_name" => $value["Inquiry"]["subscriberName"] ?? null,
                        "date" => $value["Inquiry"]["inquiryDate"] ?? null,
                        "purpose" => $value["Inquiry"]["amount"] ?? null,
                    ]);
                }

                $emi_amount =  LoanCibilAccounts::where("loan_cibil_mst_id", $loan_cibil_mst_id->id)->where("emi_amount", ">", 0)->sum("emi_amount");
                $loan_cibil_mst_id->obligate_amount = $emi_amount;
                $loan_cibil_mst_id->status = "complete";
                $loan_cibil_mst_id->save();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    "data" => $response
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                "data" => $response
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
