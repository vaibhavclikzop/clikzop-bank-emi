<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Territories extends Model
{
    protected $table = 'territories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'state',
        'district',
        'city',
        'status',
    ];
}
