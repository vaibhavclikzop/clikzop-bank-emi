<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vanillaReportYears extends Model
{
    protected $table = "loan_vanilla_report_years";
    protected $primaryKey = 'id';
    protected $fillable = [
        "vanilla_mst_id",
        "financial_year",
        "turnover",
        "gross_profit",
    ];

    public function values()
{
    return $this->hasMany(vanillaReportValues::class,'vanilla_report_year_id');
}
}
