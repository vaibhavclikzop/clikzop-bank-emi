<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanGSTRunningYearMst extends Model
{
    protected $table = 'loan_gst_running_year_mst';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'company',
        'location',
        'year',
        'financial_year',
        'average_turnover',
        'total_turnover',
        'user_id',

    ];

    public function gstRunningYearDetails()
    {
        return $this->hasMany(LoanGSTRunningYearDet::class, 'mst_id', 'id');
    }
}
