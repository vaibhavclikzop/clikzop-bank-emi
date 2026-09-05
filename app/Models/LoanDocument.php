<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    protected $table = 'loan_documents';

    protected $primaryKey = 'id';

    protected $fillable = [
        'loan_id',
        'name',
        'document_id',
        'document_type',
        'file_name',
        'original_name',
        'mime_type',
        'file_size',
        'file_path',
        'uploaded_by',
        'file_hash',

    ];

    public function userDetails()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
