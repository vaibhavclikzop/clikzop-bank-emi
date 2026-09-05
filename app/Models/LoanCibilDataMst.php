<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCibilDataMst extends Model
{
    protected $table = 'loan_cibil_data_mst';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'bank_id',
        'loan_account_no',
        'loan_amount',
        'outstanding_amount',
        'start_date',
        'loan_type_id',
        'tenure',
        'tenure_left',
        'cibil_status_id',
        'overdue',
        'emi_bank_name',
        'account_type_id',
        'paid_account_no',
        'emi_amount',
        'obligate_amount',
        'user_id',
        'customer_id',
        'file',
        'control_number',
        'report_date',
        'cibil_score',
        'full_name',
        'date_of_birth',
        'gender',
        'retry_count',
        'last_retry',
        'processed_at',
        'processing_started_at',
        'status',
        'message',
        'raw_data',
        "request_id",
        "currentCredit",
        "creditUsed",
        "OldestCreditAccountPeriod",
        "Inquires",
        "OnTimePaymentHistory",
        "CreditCardUtilization",
        "CreditMix",
        "serialNumber",
        "reportUrl",

    ];

    public function bankDetails()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id', 'id');
    }

    public function cibilStatus()
    {
        return $this->belongsTo(cibilStatus::class, 'cibil_status_id', 'id');
    }

    public function accountType()
    {
        return $this->belongsTo(accountType::class, 'account_type_id', 'id');
    }

    public function userDetails()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function loanDetails()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
}
