<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeType extends Model
{
    protected $table = 'income_type';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'active',
    ];
}
