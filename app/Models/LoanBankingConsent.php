<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanBankingConsent extends Model
{
    protected $table = 'loan_banking_consents';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'consent_id',
        'url',
        'status',
        'fl_status',
        'fl_id',
        'fl_date_time',
        'response',
        'user_id',
        'from_date',
        'to_date',

    ];

    public function loanDetails()
    {
        return $this->belongsTo(Loan::class, "loan_id");
    }
}
