<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class webhook extends Model
{

    protected $table = "webhooks";
    protected $primaryKey = 'id';

    protected $fillable = [
        'provider',
        'event',
        'request_id',
        'method',
        'ip_address',
        'headers',
        'payload',
        'http_status',
        'processed',
        'processed_at',
        'error',
        'status',
        'user_id',
        'loan_gst_details_id'
    ];

    protected $casts = [
        'headers'      => 'array',
        'processed'    => 'boolean',
        'processed_at' => 'datetime',
    ];
}
