<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanApplicants extends Model
{
    protected $table = "loan_applicants";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_id",
        "customer_id",
        "applicant_type",
        "financial_status",
        "relationship_id",
        "is_primary",
    ];

    public function applicantDetails()
    {
        return $this->belongsTo(Customers::class, "customer_id");
    }

    public function itrDetails()
    {
        return $this->hasOne(LoanITRProfile::class, "loan_applicant_id");
    }
}
