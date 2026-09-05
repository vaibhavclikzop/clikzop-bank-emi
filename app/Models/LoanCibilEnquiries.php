<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCibilEnquiries extends Model
{
    protected $table = "loan_cibil_enquiries";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_cibil_mst_id",
        "member_name",
        "date",
        "purpose",
 
    ];
}
