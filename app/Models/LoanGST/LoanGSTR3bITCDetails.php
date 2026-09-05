<?php

namespace App\Models\LoanGST;

use Illuminate\Database\Eloquent\Model;

class LoanGSTR3bITCDetails extends Model
{
    protected $table = 'loan_gstr3b_itc_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'gstr3b_details_id',

        'section',
        'type',

        'iamt',
        'camt',
        'samt',
        'csamt',
        'total',
    ];
}
