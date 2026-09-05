<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerGSTR3bITCDetails extends Model
{
    protected $table = 'customer_gstr3b_itc_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'gstr3b_details_id',

        'section',
        'type',

        'iamt',
        'camt',
        'samt',
        'csamt',
        'total',
    ];
}
