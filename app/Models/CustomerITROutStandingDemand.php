<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerITROutStandingDemand extends Model
{
    protected $table = 'customer_itr_out_standing_demands';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id',
        'customer_itr_profile_id',
        'rectification_rights',
        'date_of_service',
        'din',
        'date_of_demandRaised',
        'section_code',
        'assessment_year',
        'outstanding_demand_amount',
        'mode_of_service',
        'uploaded_by',
    ];

    protected $casts = [
        'date_of_service' => 'date',
        'date_of_demandRaised' => 'date',
        'outstanding_demand_amount' => 'decimal:2',
    ];
}
