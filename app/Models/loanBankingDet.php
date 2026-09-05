<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loanBankingDet extends Model
{
    protected $table = 'loan_banking_det';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'mst_id',
        'month',
        'month_name',
        'closing_balance_5',
        'closing_balance_15',
        'closing_balance_20',
        'closing_balance_25',
        'closing_balance_30',
        'monthly_total',
        'monthly_average',
        "loan_banking_mst_id"
    ];
    
}
