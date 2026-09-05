<?php

namespace App\Models\LoanGST;

use Illuminate\Database\Eloquent\Model;

class LoanGSTR3bDetails extends Model
{
    protected $table = 'loan_gstr3b_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'gst_details_id',
        'ret_period',

        'ttl_tax_payable',
        'ttl_tax_paid',
        'ttl_itc_paid',
        'ttl_cash_paid',

        'ttl_late_fee',
        'ttl_interest',

        'opening_balance',
        'closing_balance',

        'itc_avl_by_gstr2a',
    ];
}
