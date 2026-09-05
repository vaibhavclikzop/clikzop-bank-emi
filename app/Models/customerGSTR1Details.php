<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerGSTR1Details extends Model
{
    protected $table = "customer_gstr1_details";
    protected $primaryKey = 'id';
    protected $fillable = [
        'gst_details_id',
        'ret_period',
        'section_name',
        'checksum',
        'total_records',
        'taxable_value',
        'igst',
        'cgst',
        'sgst',
        'cess',
        'total_value',
    ];
}
