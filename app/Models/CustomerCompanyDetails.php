<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerCompanyDetails extends Model
{
    protected $table = 'customer_company_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'cin',
        'company_name',
        'status',
        'entity_class',
        'category',
        'subcategory',
        'registration_number',
        'roc_code',
        'whether_listed_or_not',
        'paid_up_capital',
        'authorised_capital',
        'number_of_members',
        'date_of_incorporation',
        'date_of_last_agm',
        'date_of_balance_sheet',
        'industry',
        'sub_industry',
        'activity_group',
        'activity_class',
        'activity_sub_class',
        'registered_address',
        'alternative_address',
        'email',
        'alternate_source_data',
        'api_response',
        'user_id',
        'pan_no',
        'pan_last4',
    ];

    protected $casts = [
        'alternate_source_data' => 'boolean',
        'paid_up_capital' => 'decimal:2',
        'authorised_capital' => 'decimal:2',
        'date_of_incorporation' => 'date',
        'date_of_last_agm' => 'date',
        'date_of_balance_sheet' => 'date',
        'api_response' => 'array',
    ];

    protected $hidden = [
        'api_response',
        'pan_no',
    ];

    protected $appends = [
        'pan_no_masked',
    ];

    public function directorDetails()
    {
        return $this->hasMany(CustomerCompanyDirectors::class, 'company_id');
    }

    public function setPanNoAttribute($value): void
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['pan_no'] = encrypt($value);

            // PAN ka last 4
            $this->attributes['pan_last4'] = substr($value, -4);
        }
    }

    public function getPanNoAttribute($value): ?string
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getPanNoMaskedAttribute(): ?string
    {
        if (! $this->pan_last4) {
            return null;
        }

        return '******'.$this->pan_last4;
    }
}
