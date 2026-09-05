<?php

namespace App\Models\LoanGST;

use Illuminate\Database\Eloquent\Model;

class LoanGSTR3BSupplyDetails extends Model
{
      protected $table = 'loan_gstr3b_supply_details';
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
