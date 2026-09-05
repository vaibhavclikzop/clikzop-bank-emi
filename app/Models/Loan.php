<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'tenant_id',
        'loan_number',
        'loan_type_id',
        'income_type_id',
        'loan_amount',
        'interest_rate',
        'tenure_months',
        'loan_purpose',
        'emi_amount',
        'total_interest',
        'total_payable',
        'start_date',
        'end_date',
        'status_id',
        'bank_name',
        'remarks',
        'kyc_verified',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function loanType()
    {
        return $this->belongsTo(loanType::class, 'loan_type_id', 'id');
    }

    public function incomeType()
    {
        return $this->belongsTo(IncomeType::class, 'income_type_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            $loan->loan_number = 'LN-' . strtoupper(uniqid());
        });
    }

    public function status()
    {
        return $this->belongsTo(Loan_Statuses::class, 'status_id');
    }

    public function loanApplicants()
    {
        return $this->hasMany(LoanApplicants::class, 'loan_id');
    }
}
