<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\LoanCibilDataMst;
use App\Models\loanLipReportDet;
use App\Models\loanLipReportMst;
use App\Models\loanVanillaReportFields;
use App\Models\LoanVanillaReportSummary;
use App\Models\vanillaField;
use App\Models\vanillaReportMst;
use App\Models\vanillaReportValues;
use App\Models\vanillaReportYears;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoanVanillaReportController extends Controller
{
    public function saveVanillaYear(Request $request)
    {

        return response()->json([
            'status' => false,
            'message' => "This api not working please connect to developer..",
            'data' => [],
        ], 422);
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'customer_id' => 'required',
            'financial_year' => 'required',
            'turn_over' => 'required',
            'gross_profit' => 'required',
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

            $vanillaReportMst = vanillaReportMst::updateOrCreate(
                [
                    "loan_id" => $request->loan_id,
                    "customer_id" => $request->customer_id,
                ],
                [
                    "user_id" => auth()->user()->id,
                ]
            );

            $vanillaReportYears =   vanillaReportYears::updateOrCreate(
                [
                    "vanilla_mst_id" => $vanillaReportMst->id,
                    "financial_year" => $request->financial_year,
                ],
                [
                    "vanilla_mst_id" => $vanillaReportMst->id,
                    "financial_year" => $request->financial_year,
                    "turnover" => $request->turn_over,
                    "gross_profit" => $request->gross_profit,
                ]
            );

            $vanillaField = vanillaField::where("status", "active")->get();
            foreach ($vanillaField as $key => $value) {
                $field =  loanVanillaReportFields::updateOrCreate([
                    "vanilla_report_mst_id" => $vanillaReportMst->id,
                    "vanilla_field_id" => $value->id,
                ], [
                    "eligibility_percentage" => 0,
                    "average" => 0,
                    "eligibility_income" => 0,
                ]);

                vanillaReportValues::updateOrCreate([
                    "vanilla_report_year_id" => $vanillaReportYears->id,
                    "field_name" => $value->field_name,

                ], [
                    "loan_vanilla_report_field_id" => $field->id,
                    "vanilla_field_id" => $value->id,
                    "display_name" => $value->display_name,
                    "display_order" => $value->display_order,
                    "amount" => 0,
                    "eligibility_percentage" => 0,
                    "eligible_amount" => 0
                ]);
            }
            LoanVanillaReportSummary::updateOrCreate([
                "loan_id" => $request->loan_id
            ], []);


            $loanLipReportMst = loanLipReportMst::firstOrCreate([
                "loan_id" => $request->loan_id,
            ]);

            loanLipReportDet::firstOrCreate([
                "loan_id" => $request->loan_id,
                "loan_lip_report_mst_id" => $loanLipReportMst->id,
                "customer_id" => $request->customer_id,
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


    public function updateVanillaValues(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_id' => 'required',
            'id' => 'required',
            'amount' => 'required',
            'eligible_percentage' => 'required',
            'loan_id' => 'required',

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


            $value = vanillaReportValues::where("id", $request->id)->first();
            $value->amount = $request->amount;
            $value->save();

            $field =  loanVanillaReportFields::with("loanVanillaReportMaster")->where("id", $request->field_id)->first();
            $field->eligible_percentage = $request->eligible_percentage;
            $field->save();


            $this->updateVanillaReportSummary($field->loanVanillaReportMaster->loan_id);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => "Save successfully",
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


    public function updateVanillaSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'id' => 'required',
            'field_name' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        try {
            $data =  LoanVanillaReportSummary::where("id", $request->id)->where("loan_id", $request->loan_id)->first();
            $data->{$request->field_name} = $request->value;
            $data->save();

            $this->updateVanillaReportSummary($request->loan_id);
            $this->updateLipReport($request->loan_id);
            return response()->json([
                'status' => true,
                'message' => "Save successfully",
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updateVanillaReportSummary(int $loan_id)
    {
        $reports = vanillaReportMst::with([
            'fields.values'
        ])->where('loan_id', $loan_id)->get();
        $totalAnnualIncome = 0;
        foreach ($reports as $report) {
            $totalEligibleIncome = 0;

            foreach ($report->fields as $field) {

                $average = round(
                    $field->values->avg('amount') ?? 0,
                    2
                );

                $eligibleIncome = round(
                    ($average * ($field->eligible_percentage ?? 0)) / 100,
                    2
                );
                $field->update([
                    'average' => $average,
                    'eligible_income' => $eligibleIncome,
                ]);
                $totalEligibleIncome += $eligibleIncome;
            }
            $report->update([
                'total' => $totalEligibleIncome,
            ]);
            $totalAnnualIncome += $totalEligibleIncome;
        }

        $LoanCibilDataMst =  LoanCibilDataMst::where("loan_id", $loan_id)->first();
        $summary = LoanVanillaReportSummary::where("loan_id", $loan_id)->first();

        $emiFactor =  $this->pmt($summary->interest_rate, $summary->tenor, 100000);
        $maxEMi = abs(($summary->appraised_monthly_income * ($summary->foir / 100)) - $summary->appraised_obligations);
        LoanVanillaReportSummary::where("loan_id", $loan_id)->update(array(
            "total_annual_income" => $totalAnnualIncome,
            "appraised_monthly_income" => $totalAnnualIncome / 12,
            "appraised_obligations" => $LoanCibilDataMst->obligate_amount,
            "net_appraised_income" => ($totalAnnualIncome / 12) - $LoanCibilDataMst->obligate_amount,
            "ltv_foir" => $summary->foir + $summary->ltv,
            "max_emi" => $maxEMi,
            "emi_factor" => $emiFactor,
            "eligibility" => $maxEMi / $emiFactor,
        ));
    }

    function pmt($annualRate, $months, $loanAmount)
    {

        $r = ($annualRate / 100) / 12;
        if ($r == 0) {
            return $loanAmount / $months;
        }
        return ($r * $loanAmount) / (1 - pow(1 + $r, -$months));
    }


    function updateLipReport(int $loan_id) {}
}
