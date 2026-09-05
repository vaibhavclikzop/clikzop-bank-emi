<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loanVanillaReportFields extends Model
{
    protected $table = "loan_vanilla_report_fields";
    protected $primaryKey = 'id';
    protected $fillable = [
        "vanilla_report_mst_id",
        "vanilla_field_id",
        "eligible_percentage",
        'average',
        'eligible_income',
    ];

    public function fieldDetails()
    {
        return $this->belongsTo(vanillaField::class, "vanilla_field_id");
    }

    public function loanVanillaReportMaster()
    {
        return $this->belongsTo(vanillaReportMst::class, "vanilla_report_mst_id");
    }

    public function values(){
        return $this->hasMany(vanillaReportValues::class,"loan_vanilla_report_field_id");
    }
}
