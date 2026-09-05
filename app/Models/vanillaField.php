<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vanillaField extends Model
{
    protected $table = "vanilla_fields";
    protected $primaryKey = 'id';

    protected $fillable = [
        "field_name",
        "display_name",
        "display_order",
        "status",
    ];
}
