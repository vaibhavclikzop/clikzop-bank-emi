<?php

namespace App\Models\LoanGST;

use Illuminate\Database\Eloquent\Model;

class LoanGSTDetailFillings extends Model
{
    protected $table = 'loan_gst_detail_fillings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'gst_details_id',
        'valid',
        'mof',
        'dof',
        'return_type',
        'ret_prd',
        'arn',
        'status',
        'due_date',
        'is_delay',
        'delay_days',
    ];

    protected $casts = [
        'dof' => 'date',
        'due_date' => 'date',
    ];
}
