<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loan_banking_transactions extends Model
{
    protected $table="loan_banking_transactions";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_id",
        "loan_banking_mst_id",
        "txn_date",
        "value_date",
        "description",
        "cheque_number",
        "transaction_id",
        "reference_number",
        "upi_id",
        "mode",
        "debit",
        "credit",
        "balance",
    ];
}
