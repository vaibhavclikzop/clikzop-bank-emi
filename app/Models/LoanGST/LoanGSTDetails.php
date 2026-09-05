<?php

namespace App\Models\LoanGST;

use Illuminate\Database\Eloquent\Model;

class LoanGSTDetails extends Model
{
    protected $table = 'loan_gst_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'company_id',
        'loan_id',
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

    public function gstr3bDetails(){
        return $this->hasMany(LoanGSTR3bDetails::class,"gst_details_id");
    }
}
