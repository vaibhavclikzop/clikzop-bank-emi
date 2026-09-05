<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loanBankingMst extends Model
{
    protected $table = 'loan_banking_mst';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'account_holder',
        'bank_id',
        'account_number',
        'account_type_id',
        'total_6_month_balance',
        'total_12_month_balance',
        'avg_6_month_balance',
        'avg_12_month_balance',
        'user_id',
        'customer_id',
        'file',
        'json_file',
        'raw_text_file',
        'bank_name',
        'statement_from',
        'statement_to',
        'account_type',
        'branch',
        'ifsc',
        'micr',
        'opening_balance',
        'closing_balance',
        'currency',
        'total_debit',
        'total_credit',
        'retry_count',
        'last_retry',
        'processed_at',
        'processing_started_at',
        'status',
        'message',
        'loan_banking_consent_id',
        'mobile',
        'current_balance',
        'current_od_limit',
        'drawing_limit',
        'name',
        'nominee',
        'pan',
        'address',

    ];

    public function monthDetails()
    {
        return $this->hasMany(loanBankingDet::class, "mst_id");
    }
}
