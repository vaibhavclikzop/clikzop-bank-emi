<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanGSTRunningYearDet extends Model
{
    protected $table = 'loan_gst_running_year_det';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'mst_id',
        'amount',
        'month',
        'month_name',
    ];
}
