<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerITRProfile extends Model
{
    protected $table = 'customer_itr_profile';
    protected $primaryKey = 'id';
    protected $fillable = [
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

    public function getItrYearlyDetails()
    {
        return $this->hasMany(CustomerITRYearly::class, "customer_itr_profile_id");
    }

    public function getItrOutStanding()
    {
        return $this->hasMany(CustomerITROutStandingDemand::class, "customer_itr_profile_id");
    }

      public function getItrFilingHistory()
    {
        return $this->hasMany(CustomerITRFilingHistory::class, "customer_itr_profile_id");
    }
}
