<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    protected $table = 'plans';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'expire_days',
        'commission',
        'commission_type',
        'status',
    ];
}
