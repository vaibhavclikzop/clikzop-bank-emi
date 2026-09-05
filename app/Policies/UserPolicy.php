<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, User $model)
    {

        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $model->tenant_id;
    }

    public function create(User $user)
    {
        return in_array($user->role, ['super_admin', 'dsa_admin', 'coordinator', 'partner']);
    }

    public function update(User $user, User $model)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $model->tenant_id;
    }

    public function delete(User $user, User $model)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $model->tenant_id;
    }
}
