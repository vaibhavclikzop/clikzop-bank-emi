<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerGSTR3bDetails extends Model
{

    protected $table = 'customer_gstr3b_details';
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


    public function gstSupplyDetails()
    {
        return $this->hasMany(customerGSTR3bSupplyDetails::class, 'gstr3b_details_id');
    }

    public function gstITCDetails()
    {
        return $this->hasMany(customerGSTR3bITCDetails::class, 'gstr3b_details_id');
    }
}
