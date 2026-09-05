<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoanITRFillingHistory extends Model
{
    protected $table = 'loan_itr_filing_history';

    protected $fillable = [
        'loan_id',
        'customer_id',
        'loan_itr_profile_id',
        'pan_no',
        'pan_no_last4',
        'assessment_year',
        'form',
        'filingType',
        'status',
        'date',
        'downloadLink',
        'loan_applicant_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $hidden = [
        "pan_no"
    ];

    protected $appends = [
        "pan_masked"
    ];
    public function setPanNoAttribute($value)
    {
        if (!empty($value) && !Str::startsWith($value, 'eyJ')) {
            $this->attributes['pan_no'] = encrypt($value);
            $this->attributes['pan_no_last4'] = substr($value, -4);
        }
    }

    public function getPanNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }
    public function getPanMaskedAttribute()
    {
        $pan = $this->pan_no;

        if (!$pan || strlen($pan) < 10) {
            return null;
        }

        return
            'XXXXXX'
            . substr($pan, -4);
    }
}
