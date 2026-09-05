<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerBanks extends Model
{
    protected $table = 'customer_banks';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'account_no',
        'account_no_last4',
        'ifsc',
        'bank_transaction_status',
        'bank_rrn',
        'bank_response',
        'status_code',
        'status_as_per_source',
        'is_valid',
        'user_id',
        'account_holder_name',
        'karza_request_id',
        'api_response',
        'account_no_hash',
        'bank_id',
        'city',
        'district',
        'micr',
        'state',
        'branch',
        'address',
        'office',
        'ifsc_master_id',
    ];

    protected $hidden = [
        'account_no',
        'api_response',
        'account_no_hash',
        'karza_request_id',
    ];

    protected $appends = [
        'account_no_masked',
    ];

    public function setAccountNoAttribute($value)
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['account_no'] = encrypt($value);
            $this->attributes['account_no_last4'] = substr($value, -4);
        }
    }

    public function getAccountNoAttribute($value)
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAccountNoMaskedAttribute()
    {
        return $this->account_no_last4
            ? 'XXXXXXXX'.$this->account_no_last4
            : null;
    }

    public function bankDetails()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function bankBranchDetails()
    {
        return $this->belongsTo(IfscMaster::class, 'ifsc_master_id');
    }
}
