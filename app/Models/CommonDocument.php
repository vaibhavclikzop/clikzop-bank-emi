<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonDocument extends Model
{
    protected $table = 'common_documents';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'variable_name',
    ];
}
