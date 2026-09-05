<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantTerritories extends Model
{
    protected $table = 'tenant_territories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tenant_id',
        'territory_id',
    ];
}
