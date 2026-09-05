<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loanLipReportMst extends Model
{
    protected $table = "loan_lip_report_mst";
    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'total_eligible_loan',
        'net_eligible_loan',
        'remarks',
    ];

    public function lipReportDetails(){
        return $this->hasMany(loanLipReportDet::class,"loan_lip_report_mst_id");
    }
}
