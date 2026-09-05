<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanITROutStandingDemand extends Model
{
    protected $table = 'loan_itr_out_standing_demands';
    protected $primaryKey = 'id';
    protected $fillable = [
        'loan_id',
        'customer_id',
        'loan_itr_profile_id',
        'rectification_rights',
        'date_of_service',
        'din',
        'date_of_demandRaised',
        'section_code',
        'assessment_year',
        'outstanding_demand_amount',
        'mode_of_service',
        'uploaded_by',
              'loan_applicant_id',
    ];

    protected $casts = [
        'date_of_service' => 'date',
        'date_of_demandRaised' => 'date',
        'outstanding_demand_amount' => 'decimal:2',
    ];
}
