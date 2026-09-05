<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCompanyDirectors extends Model
{
    protected $table = 'customer_company_directors';

    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id',
        'customer_id',
        'din',
        'pan',
        'name',
        'designation',
        'dob',
        'father_name',
        'tenure_begin_date',
        'tenure_end_date',
        'address',
        'user_id',
    ];

    protected $casts = [
        'dob' => 'date',
        'tenure_begin_date' => 'date',
        'tenure_end_date' => 'date',
    ];
}
