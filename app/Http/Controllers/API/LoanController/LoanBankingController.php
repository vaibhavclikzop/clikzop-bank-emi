<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\loan_banking_transactions;
use App\Models\LoanBankingConsent;
use App\Models\loanBankingDet;
use App\Models\loanBankingMst;
use App\Models\LoanCibilDataMst;
use App\Services\Setu\SetuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LoanBankingController extends Controller
{
    public function saveBanking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'bank_id' => 'required',
            'account_number' => 'required',
            'account_type_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        DB::beginTransaction();

        try {
            $Customers = Loan::where('id', $request->loan_id)->exists();
            if (! $Customers) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found',
                    'data' => [],
                ], 500);
            }

            $data = loanBankingMst::create([

                'user_id' => auth()->user()->id,
                'loan_id' => $request->loan_id,
                'bank_id' => $request->bank_id,
                'account_number' => $request->account_number,
                'account_type_id' => $request->account_type_id,

            ]);

            $months = [
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
                1 => 'January',
                2 => 'February',
                3 => 'March',
            ];

            foreach ($months as $month => $monthName) {

                loanBankingDet::create([

                    'loan_id' => $request->loan_id,
                    'mst_id' => $data->id,
                    'month' => $month,
                    'month_name' => $monthName,

                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => [],
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function saveBankingAmount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'loan_id' => 'required',
            'filed_name' => 'required',
            'amount' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        DB::beginTransaction();

        try {
            $Customers = Loan::where('id', $request->loan_id)->exists();
            if (! $Customers) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found',
                    'data' => [],
                ], 500);
            }

            loanBankingDet::where('id', $request->id)->where('loan_id', $request->loan_id)->update([
                $request->filed_name => $request->amount,
            ]);

            $detail = loanBankingDet::where('id', $request->id)
                ->where('loan_id', $request->loan_id)
                ->first();

            $data =
                $detail->closing_balance_5 +
                $detail->closing_balance_15 +
                $detail->closing_balance_20 +
                $detail->closing_balance_25 +
                $detail->closing_balance_30;

            loanBankingDet::where('id', $request->id)->where('loan_id', $request->loan_id)->update([
                'monthly_total' => $data,
                'monthly_average' => $data / 5,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function uploadBankStatement(Request $request)
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
        DB::beginTransaction();
        try {

            $parser = new Parser();
            $pdf = $parser->parseFile($request->file('pdf')->getRealPath());
            if (mb_strlen($pdf->gettext()) < 300) {
                return response()->json([
                    'status' => false,
                    'message' => 'Scanned PDF detected. Please upload a digitally generated PDF.',
                    'data' => [],
                ], 422);
            }
            $pages = $pdf->getPages();
            $cleanText = "";

            foreach ($pages as $index => $page) {

                $pageText = $page->getText();

                $cleanText .= "\n\n================ PAGE " . ($index + 1) . " ================\n\n";

                $cleanText .= $pageText;
            }





            $file = $request->file('pdf');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bank-statement', $fileName, 'public');

            $folder = storage_path('app/public/bank-statement-text');

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }

            $fileNameTxt = 'bank_statement_' . $Loan->id . '_' . time() . '.txt';
            $filePathTxt = $folder . '/' . $fileNameTxt;


            File::put($filePathTxt, $cleanText);



            $textLength = mb_strlen($cleanText);


            loanBankingMst::updateOrCreate(
                [
                    "loan_id" => $request->loan_id,
                    "customer_id" => $Loan->customer_id,
                ],
                [
                    "file" => $filePath,
                    "status" => "pending",
                    "raw_text_file" => 'bank-statement-text/' . $fileNameTxt,
                    "user_id" => $request->user()->id,
                ]
            );
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'PDF text extracted successfully.',
                'data' => [
                    'text' => $cleanText,
                    'textLength' => $textLength,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function createBankingConsent(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        DB::beginTransaction();

        try {
            $Loan = Loan::with("customer")->where('id', $request->loan_id)->first();
            if (! $Loan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found',
                    'data' => [],
                ], 404);
            }



            $response = app(SetuService::class)->createConsent($Loan->id, $Loan->customer->number, $request->from_date, $request->to_date);

            if ($response["status"] == true) {

                LoanBankingConsent::updateOrCreate([
                    "loan_id" => $Loan->id,
                ], [
                    "consent_id" => $response["data"]["id"],
                    "url" => $response["data"]["url"],
                    "from_date" => $request->from_date,
                    "to_date" => $request->to_date,
                    "status" => $response["data"]["status"],
                    "response" => json_encode($response),
                    "user_id" => $request->user()->id,
                ]);
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Save successfully',
                    'data' => [],
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'data' => $response,
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function checkBankingConsent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $consent = LoanBankingConsent::where('id', $request->id)->first();
        if (!$consent) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {


            if ($consent->status == "ACTIVE") {
                return response()->json([
                    'status' => true,
                    'message' => 'Consent already verified',
                    'data' => [],
                ], 200);
            }
            if ($consent->status == "COMPLETE") {
                return response()->json([
                    'status' => true,
                    'message' => 'Already fetched statement',
                    'data' => [],
                ], 200);
            }
            $response = app(SetuService::class)->checkConsent($consent->id, $consent->consent_id);
            if ($response["status"] == true && ($response['session']['status'] ?? false) == true) {


                LoanBankingConsent::updateOrCreate([
                    "id" => $consent->id,
                ], [
                    "status" => $response["data"]["status"],
                    "fl_status" => "COMPLETE",
                    "fl_id" => $response["session"]["data"]["id"],
                    "fl_date_time" => now(),
                ]);
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Save successfully',
                    'data' => $response,
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Consent not verified',
                    'data' => $response,
                ], 404);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getBankStatement(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $consent = LoanBankingConsent::with("loanDetails")->where('id', $request->id)->first();


        if (! $consent) {
            return response()->json([
                'status' => false,
                'message' => 'Consent not found',
                'data' => [],
            ], 404);
        }
        DB::beginTransaction();

        try {


            if ($consent->status == "PENDING") {
                return response()->json([
                    'status' => false,
                    'message' => 'Can not fetch statement.. consent is not active',
                    'data' => [],
                ], 500);
            }
            if ($consent->status == "COMPLETE") {
                DB::commit();
                 $this->putBankingSummary($consent->id);
                return response()->json([
                    'status' => true,
                    'message' => 'Already fetched statements',
                    'data' => [],
                ], 200);
            }

            $response = app(SetuService::class)->getBankStatement($consent->id, $consent->fl_id);
            if ($response["status"] == true) {


                $fips = $response["data"]["fips"];

                foreach ($fips as $f => $p) {
                    $accounts = $p["accounts"];

                    foreach ($accounts as $key => $value) {
                        if (($value['FIstatus'] ?? null) !== 'READY' || empty($value['data'] ?? null)) {
                            loanBankingMst::updateOrCreate(
                                [
                                    "loan_banking_consent_id" => $consent->id,
                                    "loan_id" => $consent->loan_id,
                                    "account_number" => $value["maskedAccNumber"],
                                ],
                                [
                                    "loan_id" => $request->loan_id,
                                    "customer_id" => $consent->loanDetails->customer_id,
                                    "status" => "error",
                                    "message" => $value['FIstatus'] . " , Data not found",

                                    "user_id" => $request->user()->id,
                                ]
                            );
                            continue;
                        }
                        $profile = $value["data"]["account"]["profile"]["holders"]["holder"][0];
                        $summary = $value["data"]["account"]["summary"];
                        $transaction = $value["data"]["account"]["transactions"]["transaction"];
                        $loanBankingMst =  loanBankingMst::updateOrCreate(
                            [
                                "loan_banking_consent_id" => $consent->id,
                                "account_number" => $value["data"]["account"]["maskedAccNumber"],
                            ],
                            [
                                "loan_id" => $request->loan_id,
                                "customer_id" => $consent->loanDetails->customer_id,
                                "address" => $profile["address"]  ?? null,
                                "mobile" => $profile["mobile"]  ?? null,
                                "name" => $profile["name"]  ?? null,
                                "nominee" => $profile["nominee"]  ?? null,
                                "pan" => $profile["pan"] ?? null,
                                "branch" => $summary["branch"] ?? null,
                                "current_balance" => $summary["currentBalance"] ?? null,
                                "current_od_limit" => $summary["currentODLimit"] ?? null,
                                "drawing_limit" => $summary["drawingLimit"] ?? null,
                                "ifsc" => $summary["ifscCode"] ?? null,
                                "micr" => $summary["micrCode"] ??  null,
                                "type" => $summary["type"] ?? null,
                                "statement_from" => $value["data"]["account"]["transactions"]["startDate"],
                                "statement_to" => $value["data"]["account"]["transactions"]["endDate"],
                                "user_id" => $request->user()->id,
                            ]
                        );

                        foreach ($transaction as $k => $v) {

                            $debit = 0;
                            $credit = 0;
                            if ($v["type"] == "DEBIT") {
                                $debit = $v["amount"];
                            } else {
                                $credit = $v["amount"];
                            }
                            loan_banking_transactions::updateOrCreate(
                                [
                                    "loan_id" => $consent->loan_id,
                                    "loan_banking_mst_id" => $loanBankingMst->id,
                                    "transaction_id" => $v["txnId"],
                                ],
                                [
                                    "value_date" => $v["valueDate"],
                                    "mode" => $v["mode"],
                                    "description" => $v["narration"] ?? null,
                                    "balance" => $v["currentBalance"]  ?? null,
                                    "reference_number" => $v["reference"]  ?? null,
                                    "txn_date" => $v["transactionTimestamp"]  ?? null,
                                    "debit" => $debit,
                                    "credit" => $credit,
                                ]
                            );
                        }

                        $loanBankingMst->status = "complete";
                        $loanBankingMst->save();
                    }
                }


                $consent->status = "COMPLETE";
                $consent->save();
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Save successfully',
                    'data' => [],
                ], 200);
            } else {

                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'data' => $response,
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }



    public function putBankingSummary($loanBankingConsentID)
    {
        $loanBankingMst = loanBankingMst::where("loan_banking_consent_id", $loanBankingConsentID)->where("status", "complete")->where("current_balance", "!=", 0)->get();

        foreach ($loanBankingMst as $key => $value) {
            $months = [
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
                1 => 'January',
                2 => 'February',
                3 => 'March',
            ];

            foreach ($months as $month => $monthName) {

                loanBankingDet::updateOrCreate(
                    [
                        'loan_id' => $value->loan_id,
                        'mst_id' => $value->id,
                        'month' => $month,
                    ],
                    [



                        'month_name' => $monthName,

                    ]
                );
            }





            $transactions = loan_banking_transactions::where(
                "loan_banking_mst_id",
                $value->id
            )
                ->orderBy("txn_date")
                ->get();

            $result = [];

            // Statement ka starting month
            $startDate = Carbon::parse('2022-12-01');

            // Exactly 12 months
            for ($i = 0; $i < 12; $i++) {

                $currentMonth = $startDate->copy()->addMonths($i);

                foreach ([5, 15, 25] as $day) {

                    $targetDate = $currentMonth->copy()->day($day);

                    // Sirf current month ki transactions
                    $monthTransactions = $transactions->filter(function ($txn) use ($currentMonth) {
                        $date = Carbon::parse($txn->txn_date);

                        return $date->year === $currentMonth->year
                            && $date->month === $currentMonth->month;
                    });

                    // Current month me target date tak ki latest transaction
                    $transaction = $monthTransactions
                        ->filter(function ($txn) use ($targetDate) {
                            return Carbon::parse($txn->txn_date)
                                ->lte($targetDate->endOfDay());
                        })
                        ->sortByDesc(function ($txn) {
                            return Carbon::parse($txn->txn_date)->timestamp;
                        })
                        ->first();

                    $result[] = [
                        'date' => $targetDate->format('Y-m-d'),

                        'closing_balance' => $transaction
                            ? ($transaction->balance ?? 0)
                            : 0,

                        'transaction_date' => $transaction
                            ? Carbon::parse($transaction->txn_date)->format('Y-m-d')
                            : null,
                    ];
                }
            }
            $this->storeDetData($result, $value->id);
        }
    }
    public function storeDetData($result, $mst_id)
    {
        foreach ($result as $key => $value) {

            $date = Carbon::parse($value["date"]);

            $month = $date->month;
            $day = $date->day;
                $closingBalanceColumn = "closing_balance_" . $day;
              $loan_banking_det=DB::table("loan_banking_det")
                ->where("mst_id", $mst_id)
                ->where("month", $month)
   
                ->update(array(
                    $closingBalanceColumn => $value["closing_balance"]
                ));
          
          
        }
    }
}
