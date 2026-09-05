<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class accountType extends Model
{
    protected $table = 'account_type';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];
}
