<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function view(User $user, Shipment $shipment): bool
    {
        return $user->role === 'admin'
            || $user->role === 'depot_manager'
            || $user->id === $shipment->aidRequest->user_id;
    }

    public function deliver(User $user, Shipment $shipment): bool
    {
        if ($shipment->status !== 'dispatched') {
            return false;
        }

        return $user->role === 'admin'
            || ($user->role === 'coordinator' && $user->id === $shipment->aidRequest->user_id);
    }
}
