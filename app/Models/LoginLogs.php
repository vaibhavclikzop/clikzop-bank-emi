<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLogs extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'login_logs';

    protected $fillable = [
        'name',
        'user_id',
        'tenant_id',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'device',
        'browser',
        'platform',
        'session_id',
        'status',
        'login_at',
        'logout_at',
    ];
}
