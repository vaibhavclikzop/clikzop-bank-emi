<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerGstDetails extends Model
{
    protected $table = 'customer_gst_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'company_id',
        'company_name',
        'email_id',
        'gst_in',
        'gst_in_ref',
        'mobile',
        'pan',
        'registration_name',
        'tin_number',
        'state',
        'stjCd',
        'dty',
        'stj',
        'nba',
        'ctb',
        'registration_date',
        'address',
        'trade_name',
        'ctjCd',
        'status',
        'ctj',
        'e_invoice_status',
        'user_id',
        'api_response',
    ];

    protected $casts = [
        'dof' => 'date',
        'due_date' => 'date',
        'registration_date' => 'date',
        'api_response' => 'array',
    ];

    protected $hidden = [
        'api_response',
    ];

    public function gstFillingDetails()
    {
        return $this->hasMany(customerGstDetailFilling::class, 'gst_details_id');
    }

    public function gstr1Details()
    {
        return $this->hasMany(customerGSTR1Details::class, 'gst_details_id');
    }
    
    public function gstr3bDetails()
    {
        return $this->hasMany(customerGSTR3bDetails::class, 'gst_details_id');
    }
}
