<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCibilAccounts extends Model
{
    protected $table = "loan_cibil_accounts";
    protected $primaryKey = 'id';
    protected $fillable = [
        "loan_cibil_mst_id",
        "member_name",
        "account_type",
        "account_number",
        "ownership_indicator",
        "account_status",
        "credit_limit",
        "sanctioned_amount",
        "current_balance",
        "cash_limit",
        "amount_overdue",
        "rate_of_interest",
        "repayment_tenure",
        "emi_amount",
        "payment_frequency",
        "date_opened",
        "date_closed",
        "date_reported",
        "payment_history",
    ];
}
