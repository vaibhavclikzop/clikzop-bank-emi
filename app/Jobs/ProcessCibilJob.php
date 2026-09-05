<?php

namespace App\Jobs;

use App\Models\LoanCibilAccounts;
use App\Models\LoanCibilDataMst;
use App\Models\LoanCibilEnquiries;
use App\Models\LoanCibilIdentifications;
use App\Services\AIService\geminiService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProcessCibilJob implements ShouldQueue
{
    use Queueable;
    protected $cibilID;
    /**
     * Create a new job instance.
     */
    public function __construct($cibilID = null)
    {
        $this->cibilID = $cibilID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {


            $data = LoanCibilDataMst::find($this->cibilID);

            if ($data->status == "processing") {
                $filePath = public_path($data->json_file);

                if (File::exists($filePath)) {

                    $response = json_decode(File::get($filePath), true);
                    $data->control_number = $response["report_summary"]["control_number"];
                    $data->report_date = !empty($response['report_summary']['report_date'])
                        ? Carbon::createFromFormat('d/m/Y', $response['report_summary']['report_date'])->format('Y-m-d')
                        : null;
                    $data->cibil_score = $response["report_summary"]["cibil_score"];
                    $data->full_name = $response["personal_information"]["full_name"];
                    $data->date_of_birth = $response["personal_information"]["date_of_birth"];
                    $data->gender = $response["personal_information"]["gender"];
                    $data->save();
                    LoanCibilIdentifications::where("loan_cibil_mst_id", $data->id)->delete();

                    foreach ($response["identification_details"] as $key => $value) {

                        LoanCibilIdentifications::create([
                            "loan_cibil_mst_id" => $data->id,
                            "type" => $value["type"],
                            "number" => $value["number"],
                            "issue_date" => $value["issue_date"],
                            "expiry_date" => $value["expiry_date"],
                        ]);
                    }
                    LoanCibilAccounts::where("loan_cibil_mst_id", $data->id)->delete();
                    foreach ($response["account_details"] as $key => $value) {
                        LoanCibilAccounts::create([
                            "loan_cibil_mst_id"   => $data->id,
                            "member_name"         => $value["member_name"] ?? null,
                            "account_type"        => $value["account_type"] ?? null,
                            "account_number"      => $value["account_number"] ?? null,
                            "ownership_indicator" => $value["ownership_indicator"] ?? null,
                            "account_status"      => $value["account_status"] ?? null,
                            "credit_limit"        => $value["credit_limit"] ?? null,
                            "sanctioned_amount"   => $value["sanctioned_amount"] ?? null,
                            "current_balance"     => $value["current_balance"] ?? null,
                            "cash_limit"          => $value["cash_limit"] ?? null,
                            "amount_overdue"      => $value["amount_overdue"] ?? null,
                            "rate_of_interest"    => $value["rate_of_interest"] ?? null,
                            "repayment_tenure"    => $value["repayment_tenure"] ?? null,
                            "emi_amount"          => $value["emi_amount"] ?? null,
                            "payment_frequency"   => $value["payment_frequency"] ?? null,
                            "date_opened"         => $value["date_opened"] ?? null,
                            "date_closed"         => $value["date_closed"] ?? null,
                            "date_reported"       => $value["date_reported"] ?? null,
                            "payment_history"     => json_encode($value["payment_history"]) ?? null,
                        ]);
                    }
                    LoanCibilEnquiries::where("loan_cibil_mst_id", $data->id)->delete();
                    foreach ($response["enquiry_details"] as $key => $value) {
                        LoanCibilEnquiries::create([
                            "loan_cibil_mst_id"   => $data->id,
                            "member_name" => $value["member_name"],
                            "date" => $value["date"],
                            "purpose" => $value["purpose"],
                        ]);
                    }

                    $emi_amount = LoanCibilAccounts::where("loan_cibil_mst_id", $data->id)
                        ->whereRaw("LOWER(account_status) = ?", ["open"])
                        ->sum("emi_amount");


                    $data->obligate_amount = $emi_amount ?? 0;
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
                $response = app(geminiService::class)->extractData($data->id);
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
