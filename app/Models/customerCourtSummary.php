<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerCourtSummary extends Model
{
    protected $table = "customer_court_summary";
    protected $primaryKey = 'id';
    protected $fillable = [
        "customer_id",
        "severity_total",
        "severity_high_relevance",
        "severity_high",
        "severity_medium",
        "severity_low",
        "civil_cases",
        "criminal_cases",
        "pending_cases",
        "disposed_cases",
        "not_available_cases",
        "total_cases",
        "district_courts",
        "high_courts",
        "consumer_courts",
        "supreme_courts",
        "tribunal_courts",
        "rera_courts",
        "confidence_level",
        "api_response",
        "user_id",
    ];
    protected $casts = [
        'api_response' => 'array',
    ];

    protected $hidden = [
        "api_response"
    ];

    public function getCourtSummary()
    {
        return $this->hasMany(customerCourtSummary::class, "customer_id");
    }
}
