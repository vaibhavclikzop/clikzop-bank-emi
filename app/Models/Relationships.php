<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relationships extends Model
{
    protected $table = "relationships";
    protected $primaryKey = 'id';
    protected $fillable = [
        "name",
        "active",
    ];
}
