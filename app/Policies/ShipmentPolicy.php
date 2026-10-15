<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->canManageShipments();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }

    public function addEvent(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }

    public function updateLocation(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }

    public function archive(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }

    public function export(User $user): bool
    {
        return $user->canManageShipments();
    }
}
