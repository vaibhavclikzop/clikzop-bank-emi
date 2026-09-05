<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'mobile',
        'status',
        'domain',
        'expiry_date',
        'pan_no',
        'aadhar_no',
        'gst_in',
        'state',
        'district',
        'city',
        'address',
        'pincode',
        'bank_name',
        'bank_account_no',
        'ifsc_code',

    ];

    protected $hidden = [
        'pan_no',
        'aadhar_no',
        'bank_account_no',
        'gst_in',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function territory()
    {
        return $this->hasMany(TenantTerritories::class);
    }

    public function setPanNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['pan_no'] = encrypt($value);
            $this->attributes['pan_last4'] = substr($value, -4);
        }
    }

    public function getPanNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setAadharNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['aadhar_no'] = encrypt($value);
            $this->attributes['aadhar_last4'] = substr($value, -4);
        }
    }

    public function getAadharNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // public function setGstInAttribute($value)
    // {
    //     if (!empty($value) && !Str::startsWith($value, 'eyJ')) {
    //         $this->attributes['gst_in'] = encrypt($value);
    //     }
    // }

    // public function getGstInAttribute($value)
    // {
    //     try {
    //         return $value ? decrypt($value) : null;
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }

    public function setBankAccountNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['bank_account_no'] = encrypt($value);
            $this->attributes['bank_account_no_last4'] = substr($value, -4);
        }
    }

    public function getBankAccountNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPanMaskedAttribute()
    {
        return $this->pan_last4 ? 'XXXXXX'.$this->pan_last4 : null;
    }

    public function getAadharMaskedAttribute()
    {
        return $this->aadhar_last4 ? 'XXXX-XXXX-'.$this->aadhar_last4 : null;
    }

    public function getBankMaskedAttribute()
    {
        return $this->bank_account_no_last4
            ? 'XXXXXX'.$this->bank_account_no_last4
            : null;
    }
}
