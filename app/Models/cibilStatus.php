<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cibilStatus extends Model
{
    protected $table = 'cibil_status';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];
}
