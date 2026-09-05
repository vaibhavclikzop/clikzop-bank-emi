<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vanillaReportMst extends Model
{
    protected $table = "loan_vanilla_report_mst";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_id",
        "customer_id",
        "applicant_type",
        "total",
        "user_id",
    ];

    public function customerDetails()
    {
        return $this->belongsTo(Customers::class, "customer_id");
    }

    public function years()
    {
        return $this->hasMany(vanillaReportYears::class, "vanilla_mst_id");
    }


    public function fields()
    {
        return $this->hasMany(loanVanillaReportFields::class, 'vanilla_report_mst_id');
    }

    public function summary()
    {
        return $this->hasOne(LoanVanillaReportSummary::class, 'loan_id', 'loan_id');
    }
}
