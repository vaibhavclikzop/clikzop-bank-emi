<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'banks';

    protected $fillable = [
        'name',
        'ifsc_code',
        'branch_name',
        'bank_code',
        'logo_url',
        'website',
        'category',
        'active',
        'nick_name',
        'institute_id',
    ];

    public function getLogoUrlAttribute($value)
    {
        return $value
            ? asset('banks/'.$value)
            : null;
    }
}
