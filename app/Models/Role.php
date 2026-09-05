<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'tenant_id',
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {

            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->role === 'super_admin') {
                return;
            }

            if ($user->tenant_id == 0) {
                $query->whereRaw('1 = 0');

                return;
            }

            if (! is_null($user->tenant_id)) {
                $query->where('name', '!=', 'super_admin');
            }
        });
    }
}
