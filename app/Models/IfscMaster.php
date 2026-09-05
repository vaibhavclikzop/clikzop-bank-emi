<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IfscMaster extends Model
{
    protected $table = 'ifsc_masters';

    protected $primaryKey = 'id';

    protected $fillable = [
        'bank_id',
        'name',
        'branch',
        'address',
        'city',
        'district',
        'state',
        'micr',
        'office',
        'ifsc',
        'bank_code',

    ];
}
