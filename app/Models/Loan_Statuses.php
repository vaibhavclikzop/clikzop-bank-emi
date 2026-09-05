<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan_Statuses extends Model
{
    protected $table = 'loan_status';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'step_order',
        'is_active',
    ];
}
