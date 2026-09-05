<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    protected $table = 'documents';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'income_type_id',

    ];

    public function incomeType()
    {
        return $this->belongsTo(incomeType::class, 'income_type_id', 'id');
    }
}
