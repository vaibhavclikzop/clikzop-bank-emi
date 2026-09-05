<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerGSTR3bSupplyDetails extends Model
{
    protected $table = 'customer_gstr3b_supply_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'gstr3b_details_id',

        'type',

        'txval',
        'iamt',
        'camt',
        'samt',
        'csamt',
    ];
}
