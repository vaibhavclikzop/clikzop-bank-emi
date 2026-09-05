<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vanillaReportValues extends Model
{
    protected $table = "loan_vanilla_report_values";
    protected $primaryKey = 'id';
    protected $fillable = [
        'loan_vanilla_report_field_id',
        "vanilla_report_year_id",
        "vanilla_field_id",
        "field_name",
        "display_name",
        "amount",
        "eligibility_percentage",
        "eligible_amount",
        "display_order",
    ];
}
