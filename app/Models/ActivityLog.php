<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'module',
        'action',
        'record_id',
        'old_data',
        'new_data',
        'ip',
    ];
}
