<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanVanillaReportSummary extends Model
{
    protected $table="loan_vanilla_report_summary";
    protected $primaryKey = 'id';

    protected $fillable = [
        "loan_id",
        "total_annual_income",
        "appraised_monthly_income",
        "appraised_obligations",
        "net_appraised_income",
        "foir",
        "ltv",
        "ltv_foir",
        "max_emi",
        "tenor",
        "interest_rate",
        "emi_factor",
        "eligibility",
        "double",
    ];
}
