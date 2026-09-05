<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loanLipReportDet extends Model
{

    protected $table = "loan_lip_report_det";
    protected $primaryKey = 'id';
    protected $fillable = [
        'loan_id',
        'loan_lip_report_mst_id',
        'customer_id',
        'applicant_type',
        'loan_amount',
        'profit_before_tax_current',
        'profit_before_tax_previous',
        'total_profits',
        'lip_income',
        'depreciation_current',
        'depreciation_previous',
        'avg_depreciation',
        'total_cash_profits',
        'other_income',
        'total_monthly_income',
        'gross_eligible_income',
        'foir',
        'interest_rate',
        'tenor',
        'emi_per_lakh',
        'max_eligible_loan',
    ];
}
