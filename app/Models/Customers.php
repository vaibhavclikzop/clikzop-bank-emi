<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Customers extends Model
{
    protected $fillable = [
        'name',
        'number',
        'email',
        'password',
        'dob',
        'doa',
        'state',
        'district',
        'city',
        'address',
        'country',
        'pincode',
        'pan_no',
        'aadhar_no',
        'driving_license_no',
        'passport_no',
        'voter_id_no',
        'gst_no',
        'signup_document_type',
        'signup_document',
        'created_by',
        'user_id',
        'tenant_id',
        'pan_verified',
        'adhar_verified',
    ];

    protected $hidden = [
        'password',
        'pan_no',
        'aadhar_no',
        'driving_license_no',
        'passport_no',
        'voter_id_no',
        'signup_document',

    ];

    protected $appends = [
        'pan_masked',
        'aadhar_masked',
        'dl_masked',
        'passport_masked',
        'voter_masked',
        'masked_number',
    ];

    public function getMaskedNumberAttribute()
    {
        if (! $this->number) {
            return null;
        }

        return substr($this->number, 0, 5).'*****';
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function setSignupDocumentAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['signup_document'] = encrypt($value);
            $this->attributes['signup_document_last4'] = substr($value, -4);
        }
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

    public function getPanMaskedAttribute()
    {
        return $this->pan_last4 ? 'XXXXXX'.$this->pan_last4 : null;
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

    public function getAadharMaskedAttribute()
    {
        return $this->aadhar_last4 ? 'XXXXXXXX'.$this->aadhar_last4 : null;
    }

    public function setDrivingLicenseNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['driving_license_no'] = encrypt($value);
            $this->attributes['dl_last4'] = substr($value, -4);
        }
    }

    public function getDrivingLicenseNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDlMaskedAttribute()
    {
        return $this->dl_last4 ? 'XXXXXX'.$this->dl_last4 : null;
    }

    public function setPassportNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['passport_no'] = encrypt($value);
            $this->attributes['passport_no_last4'] = substr($value, -4);
        }
    }

    public function getPassportNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPassportMaskedAttribute()
    {
        return $this->passport_no_last4 ? 'XXXXXX'.$this->passport_no_last4 : null;
    }

    public function setVoterIdNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['voter_id_no'] = encrypt($value);
            $this->attributes['voter_id_no_last4'] = substr($value, -4);
        }
    }

    public function getVoterIdNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getVoterMaskedAttribute()
    {
        return $this->voter_id_no_last4 ? 'XXXXXX'.$this->voter_id_no_last4 : null;
    }

    public function userDetails()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function CustomerBankDetails()
    {
        return $this->hasMany(CustomerBanks::class, 'customer_id');
    }

    public function CustomerDocuments()
    {
        return $this->hasMany(CustomerDocuments::class, 'customer_id');
    }
}
