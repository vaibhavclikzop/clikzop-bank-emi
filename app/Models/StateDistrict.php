<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateDistrict extends Model
{
    protected $table = 'state_district';

    protected $primaryKey = 'id';

    protected $fillable = [
        'state',
        'district',
    ];
}
