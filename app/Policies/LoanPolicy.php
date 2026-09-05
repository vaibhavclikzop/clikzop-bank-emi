<?php

namespace App\Policies;

use App\Models\Customers;
use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function viewAny(User $user)
    {
        return true;
    }

    public function create(User $user, Customers $customer)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $customer->tenant_id;
    }

    public function update(User $user, Loan $loan)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $loan->tenant_id;
    }

    public function delete(User $user, Loan $loan)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->tenant_id === $loan->tenant_id;
    }
}
