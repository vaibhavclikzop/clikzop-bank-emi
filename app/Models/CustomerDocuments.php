<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerDocuments extends Model
{
    protected $table = 'customer_documents';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'document_id',
        'document_name',
        'document_no',
        'document_no_last4',
        'country',
        'state',
        'city',
        'district',
        'address',
        'pincode',
        'file',
        'is_verified',
        'verified_at',
        'user_id',

    ];

    protected $hidden = [
        'document_no',

    ];

    protected $appends = [
        'document_no_masked',
    ];

    public function setDocumentNoAttribute($value): void
    {
        if (! empty($value) && ! Str::startsWith($value, 'eyJ')) {
            $this->attributes['document_no'] = encrypt($value);
            $this->attributes['document_no_last4'] = substr($value, -4);
        }
    }

    public function getDocumentNoAttribute($value): ?string
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return $value; // old unencrypted records
        }
    }

    public function getDocumentNoMaskedAttribute(): ?string
    {
        if (! $this->document_no_last4) {
            return null;
        }

        return 'XXXXXX'.$this->document_no_last4;
    }
}
