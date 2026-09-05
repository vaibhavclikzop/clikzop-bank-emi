<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'parent_id',
        'tenant_id',
        'role',
        'company_name',
        'gst_in',
        'state',
        'district',
        'city',
        'address',
        'pincode',
        'active',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasPermission($permission)
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->contains($permission);
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
            if (! empty($user->tenant_id)) {
                $query->where('tenant_id', $user->tenant_id);
            }
        });
    }
}
