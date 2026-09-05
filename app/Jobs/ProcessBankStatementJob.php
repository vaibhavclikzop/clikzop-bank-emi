<?php

namespace App\Jobs;

use App\Models\loan_banking_transactions;
use App\Models\loanBankingMst;
use App\Services\AIService\geminiBankService;
use App\Services\Banking\BankingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use Carbon\Carbon;

class ProcessBankStatementJob implements ShouldQueue
{
    use Queueable;
    protected $bankStatementID;
    /**
     * Create a new job instance.
     */
    public function __construct($bankStatementID = null)
    {
        $this->bankStatementID = $bankStatementID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {



        DB::beginTransaction();
        try {


            $data = loanBankingMst::find($this->bankStatementID);

            if ($data->status == "processing") {
                $filePath = public_path($data->json_file);

                if (File::exists($filePath)) {

                    $response = json_decode(File::get($filePath), true);
                    foreach ($response as $key => $value) {
                        if ($value["chunk_no"] == 1) {
                            $data->bank_name = $value["response"]["statement_summary"]["bank_name"];
                            $data->statement_from = $value["response"]["statement_summary"]["statement_from"];
                            $data->statement_to = $value["response"]["statement_summary"]["statement_to"];
                            $data->account_type = $value["response"]["account_information"]["account_type"];
                            $data->branch = $value["response"]["account_information"]["branch"];
                            $data->ifsc = $value["response"]["account_information"]["ifsc"];
                            $data->micr = $value["response"]["account_information"]["micr"];
                            $data->account_holder = $value["response"]["account_information"]["account_holder"];
                            $data->account_number = $value["response"]["account_information"]["account_number"];
                            $data->save();
                            loan_banking_transactions::where("loan_banking_mst_id", $data->id)->delete();
                            foreach ($value["response"]["transactions"] as $k => $v) {
                                loan_banking_transactions::create([
                                    "loan_id" => $data->loan_id,
                                    "loan_banking_mst_id" => $data->id,
                                    "txn_date"         => formatDate($v['txn_date']),
                                    "value_date"       => formatDate($v['value_date']),
                                    "description"      => $v['description'],
                                    "cheque_number"    => $v['cheque_number'],
                                    "transaction_id"   => $v['transaction_id'],
                                    "reference_number" => $v['reference_number'],
                                    "upi_id"           => $v['upi_id'],
                                    "mode"             => $v['mode'],
                                    "debit"            => $v['debit'],
                                    "credit"           => $v['credit'],
                                    "balance"          => $v['balance'],
                                ]);
                            }
                        } else {
                            foreach ($value["response"]["transactions"] as $k => $v) {
                                loan_banking_transactions::create([
                                    "loan_id" => $data->loan_id,
                                    "loan_banking_mst_id" => $data->id,
                                    "txn_date"         => formatDate($v['txn_date']),
                                    "value_date"       => formatDate($v['value_date']),
                                    "description"      => $v['description'],
                                    "cheque_number"    => $v['cheque_number'],
                                    "transaction_id"   => $v['transaction_id'],
                                    "reference_number" => $v['reference_number'],
                                    "upi_id"           => $v['upi_id'],
                                    "mode"             => $v['mode'],
                                    "debit"            => $v['debit'],
                                    "credit"           => $v['credit'],
                                    "balance"          => $v['balance'],
                                ]);
                            }
                        }
                    }
                    $result = app(BankingService::class)->calculate($data->id, $data->loan_id);

                    $data->status = "complete";
                    $data->message = "Completed successfully";
                    $data->processed_at = now();
                    $data->save();
                } else {
                    $data->status = "error";
                    $data->message = "File not found";
                    $data->processed_at = now();
                    $data->save();
                }
            } else if ($data->status == "pending") {
                $response = app(geminiBankService::class)->extractData($data->id);
                if ($response["status"] == false) {
                    $data->status = "error";
                    $data->message = $response["message"];
                    $data->processed_at = now();
                    $data->save();
                }
            }


            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $data->status = "error";
            $data->processed_at = now();
            $data->message = $th->getMessage();
            $data->save();
        }
    }
}
