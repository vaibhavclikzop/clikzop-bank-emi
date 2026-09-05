<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CibilScores extends Model
{
    protected $table = 'cibil_scores';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pan_no',
        'mobile_number',
        'full_name',
        'current_credit',
        'credit_used',
        'report_url',
        'web_url',
        'response_key',
        'Oldest_credit_account_period',
        'inquires',
        'on_time_payment_history',
        'credit_card_utilization',
        'credit_mix',
        'date',
        'risk_score',
        'population_rank',
        'Forename',
        'user_id',
    ];
}
