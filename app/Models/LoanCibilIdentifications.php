<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCibilIdentifications extends Model
{
    protected $table = "loan_cibil_identifications";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_cibil_mst_id",
        "type",
        "number",
        "issue_date",
        "expiry_date",
    ];
}
