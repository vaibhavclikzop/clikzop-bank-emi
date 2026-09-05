<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'loan_type';

    protected $fillable = [
        'name',
    ];
}
