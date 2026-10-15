<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->is_active;
    }

    /**
     * Full contact details (phone, street address) are limited to
     * staff who manage shipments or have elevated access.
     */
    public function viewContactDetails(User $user, Customer $customer): bool
    {
        return $user->canManageShipments();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->canManageShipments();
    }

    public function flag(User $user, Customer $customer): bool
    {
        return $user->canManageShipments();
    }

    public function export(User $user): bool
    {
        return $user->canManageShipments();
    }
}
