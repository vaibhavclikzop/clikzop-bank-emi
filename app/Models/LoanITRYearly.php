<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoanITRYearly extends Model
{
    protected $table = "loan_itr_yearly";
    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'customer_id',
        'loan_itr_profile_id',
        'form_name',
        'form_version',
        'description',
        'financial_year',
        'schema_version',
        'assessment_year',
        'filing_date',

        'immovable_assets',
        'movable_assets',
        'financial_assets',
        'total_liabilities',

        'refund',
        'taxes_paid',
        'aggregate_liability',
        'net_tax_liability',
        'total_interest_and_fee_payable',
        'total_advance_tax_paid',
        'total_tds_claimed',
        'total_tcs_claimed',
        'total_self_assessment_tax_paid',
        'amount_payable',

        'salary',
        'house_property',
        'other_sources',
        'capital_gains',

        'bank_name',
        'account_no',
        'account_no_last4',
        'ifsc_code',
        'use_for_refund',

        'delay',
        'default',
        'revised',
        'late_fee',
        'interest_fee',
        'demand_notice',
        'loan_applicant_id',
    ];

    protected $hidden = [
        "account_no"
    ];
    protected $appends = [
        "account_no_masked"
    ];
    public function setAccountNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['account_no'] = encrypt($value);
            $this->attributes['account_no_last4'] = substr($value, -4);
        }
    }

    public function getAccountNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getAccountNoMaskedAttribute()
    {
        $accountNo = $this->account_no;

        if (! $accountNo) {
            return null;
        }

        $length = strlen($accountNo);

        if ($length <= 4) {
            return $accountNo;
        }

        return str_repeat('*', $length - 4) . substr($accountNo, -4);
    }
    public function itrYearlyFinancial()
    {
        return $this->hasMany(LoanITRYearlyFinancials::class, "loan_itr_yearly_id");
    }
}
