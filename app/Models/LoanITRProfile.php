<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoanITRProfile extends Model
{
    protected $table = 'loan_itr_profile';
    protected $primaryKey = 'id';
    protected $fillable = [
        'loan_id',
        'customer_id',
        'pan_no',
        'pan_no_last4',
        'name',
        'dob',
        'aadhaar',
        'passport_no',
        'emplyrCat',
        'emplyr_cat',
        'status_of_entity',
        'residential_status',
        'pin_code',
        'state_code',
        'country_code',
        'residence_no',
        'road_or_street',
        'residence_name',
        'locality_of_area',
        'city',
        'std',
        'email',
        'phone_no',
        'email2',
        'mobile',
        'mobile2',
        'excelDownloadLink',
        'pdfDownloadLink',
        'user_id',
        'api_response',
        "type",
        'loan_applicant_id',
    ];
    protected $hidden = [
        "api_response",
        "pan_no",
    ];
    protected $casts = [
        'dob' => 'date',
        'api_response' => 'array',
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

        return  'XXXXXX'
            . substr($pan, -4);
    }

    public function itrYearlyDetails()
    {
        return $this->hasMany(LoanITRYearly::class, "loan_itr_profile_id");
    }

    public function itrFillingHistory()
    {
        return $this->hasMany(LoanITRFillingHistory::class, "loan_itr_profile_id");
    }

    public function itrOutStandingDemands()
    {
        return $this->hasMany(LoanITROutStandingDemand::class, "loan_itr_profile_id");
    }

}
