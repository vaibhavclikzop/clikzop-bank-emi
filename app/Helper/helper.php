<?php

use App\Models\LoanCibilDataMst;
use App\Models\LoanITRYearlyFinancials;
use App\Models\loanLipReportDet;
use App\Models\loanLipReportMst;
use App\Models\loanVanillaReportFields;
use App\Models\LoanVanillaReportSummary;
use App\Models\vanillaReportMst;
use App\Models\vanillaReportValues;
use App\Models\vanillaReportYears;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


if (! function_exists('isSuperAdmin')) {
    function isSuperAdmin($user = null)
    {
        $user = $user ?: Auth::user();

        if (! $user) {
            return false;
        }

        return $user->role && $user->role->name === 'super_admin';
    }
}






function updateVanillaValues($field_id, $id, $amount, $eligible_percentage, $loan_id)
{





    try {

        $value = vanillaReportValues::where("id", $id)->first();
        $value->amount = $amount;
        $value->save();
        $field =  loanVanillaReportFields::with("loanVanillaReportMaster")->where("id", $field_id)->first();
        $field->eligible_percentage = $eligible_percentage;
        $field->save();
        updateVanillaReportSummary($field->loanVanillaReportMaster->loan_id);
        return  true;
    } catch (\Throwable $th) {
        return false;
    }
}


function updateVanillaValuesSalaried($loan_id, $amount, $financial_year)
{

    $vanillaReportMst = vanillaReportMst::with([
        'years' => function ($query) use ($financial_year) {
            $query->where('financial_year', $financial_year);
        }
    ])
        ->where('loan_id', $loan_id)
        ->whereHas('years', function ($query) use ($financial_year) {
            $query->where('financial_year', $financial_year);
        })
        ->first();

    $year = $vanillaReportMst?->years->first();

    $value = vanillaReportValues::where("vanilla_field_id", 7)->where("vanilla_report_year_id", $year->id)->first();

    $value->amount = $amount;
    $value->save();
    $field =  loanVanillaReportFields::with("loanVanillaReportMaster")->where("id", $value->loan_vanilla_report_field_id)->first();
    $field->eligible_percentage = 75;
    $field->save();
    updateVanillaReportSummary($field->loanVanillaReportMaster->loan_id);
    return  true;
}



function updateVanillaValuesBusiness($loan_id,  $financial_year, $LoanITRYearly_id)
{

    $vanillaReportMst = vanillaReportMst::with([
        'years' => function ($query) use ($financial_year) {
            $query->where('financial_year', $financial_year);
        }
    ])
        ->where('loan_id', $loan_id)
        ->whereHas('years', function ($query) use ($financial_year) {
            $query->where('financial_year', $financial_year);
        })
        ->first();

    $year = $vanillaReportMst?->years->first();
    $LoanITRYearlyFinancials = LoanITRYearlyFinancials::where("loan_itr_yearly_id", $LoanITRYearly_id)->where("loan_id", $loan_id)->first();
    $value = vanillaReportValues::where("vanilla_field_id", 1)->where("vanilla_report_year_id", $year->id)->first();
    $value->amount = $LoanITRYearlyFinancials->profit_after_tax ?? 0;
    $value->save();

    $value = vanillaReportValues::where("vanilla_field_id", 3)->where("vanilla_report_year_id", $year->id)->first();
    $value->amount = $LoanITRYearlyFinancials->interest_expense ?? 0;
    $value->save();

    $value = vanillaReportValues::where("vanilla_field_id", 7)->where("vanilla_report_year_id", $year->id)->first();
    $value->amount = $LoanITRYearlyFinancials->income_from_salary ?? 0;
    $value->save();

    $value = vanillaReportValues::where("vanilla_field_id", 8)->where("vanilla_report_year_id", $year->id)->first();
    $value->amount = $LoanITRYearlyFinancials->income_from_os ?? 0;
    $value->save();

    $value = vanillaReportValues::where("vanilla_field_id", 11)->where("vanilla_report_year_id", $year->id)->first();
    $value->amount = $LoanITRYearlyFinancials->total_tax ?? 0;
    $value->save();

    $field =  loanVanillaReportFields::with("loanVanillaReportMaster")->where("id", $value->loan_vanilla_report_field_id)->first();
    $field->eligible_percentage = 75;
    $field->save();
    updateVanillaReportSummary($field->loanVanillaReportMaster->loan_id);
    return  true;
}


function updateVanillaReportYear($id, $amount, $loan_id)
{
    vanillaReportYears::where("id", $id)->update(array(
        "turnover" => $amount
    ));
    updateVanillaReportSummary($loan_id);
}


function updateVanillaReportSummary(int $loan_id)
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

    $emiFactor =  pmt($summary->interest_rate, $summary->tenor, 100000);
    $maxEMi = abs(($summary->appraised_monthly_income * ($summary->foir / 100)) - $summary->appraised_obligations);
    LoanVanillaReportSummary::where("loan_id", $loan_id)->update(array(
        "total_annual_income" => $totalAnnualIncome,
        "appraised_monthly_income" => $totalAnnualIncome / 12,
        "appraised_obligations" => $LoanCibilDataMst?->obligate_amount ?? 0,
        "net_appraised_income" => ($totalAnnualIncome / 12) - ($LoanCibilDataMst?->obligate_amount ?? 0),
        "ltv_foir" => $summary->foir + $summary->ltv,
        "max_emi" => $maxEMi,
        "emi_factor" => $emiFactor,
        "eligibility" => $maxEMi / $emiFactor,
    ));

    generateLIPReport($loan_id);
}

function pmt($annualRate, $months, $loanAmount)
{

    $r = ($annualRate / 100) / 12;
    if ($r == 0) {
        return $loanAmount / $months;
    }
    return ($r * $loanAmount) / (1 - pow(1 + $r, -$months));
}



function formatDate($date)
{
    if (empty($date)) {
        return null;
    }

    // Already DateTime object
    if ($date instanceof \DateTime) {
        return $date->format('Y-m-d');
    }

    $date = trim($date);

    // Excel Serial Number
    if (is_numeric($date) && strlen($date) <= 6) {
        try {
            return ExcelDate::excelToDateTimeObject($date)->format('Y-m-d');
        } catch (\Exception $e) {
        }
    }


    $formats = [
        'd/m/Y',
        'd-m-Y',
        'd.m.Y',
        'd M Y',
        'd F Y',

        'd/m/y',
        'd-m-y',
        'd.m.y',

        'Y-m-d',
        'Y/m/d',
        'Y.m.d',

        'm/d/Y',
        'm-d-Y',
        'm/d/y',
        'm-d-y',

        'd-M-Y',
        'd-M-y',
        'd-F-Y',
    ];

    foreach ($formats as $format) {
        try {
            $dt = Carbon::createFromFormat($format, $date);
            if ($dt && $dt->format($format) == $date) {
                return $dt->format('Y-m-d');
            }
        } catch (\Exception $e) {
        }
    }


    try {
        return Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}


function generateLIPReport($loan_id)
{
    $vanillaReportMst = vanillaReportMst::with("years.values")->where("loan_id", $loan_id)->first();
    $latestYear = $vanillaReportMst->years->sortByDesc('financial_year')->first();
    $previousYear = $vanillaReportMst->years->sortByDesc('financial_year')->skip(1)->first();
    $loanLipReportMst = loanLipReportMst::with("lipReportDetails")->where("loan_id", $loan_id)->first();

    $latestYearDetails =  $latestYear->values->where("vanilla_field_id", 1)->first();


    foreach ($loanLipReportMst->lipReportDetails as $key => $value) {

        loanLipReportDet::where("id", $value->id)->update(array(
            "profit_before_tax_current" => $latestYearDetails->amount
        ));
    }
}
