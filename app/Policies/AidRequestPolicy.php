<?php

namespace App\Policies;

use App\Models\AidRequest;
use App\Models\User;

class AidRequestPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'coordinator']);
    }

    public function view(User $user, AidRequest $aidRequest): bool
    {
        return $user->role === 'admin'
            || $user->role === 'depot_manager'
            || $user->id === $aidRequest->user_id;
    }

    public function reject(User $user, AidRequest $aidRequest): bool
    {
        return in_array($user->role, ['admin', 'depot_manager'])
            && $aidRequest->status === 'pending';
    }

    public function dispatch(User $user, AidRequest $aidRequest): bool
    {
        return in_array($user->role, ['admin', 'depot_manager'])
            && $aidRequest->status === 'pending';
    }

    public function confirmDelivery(User $user, AidRequest $aidRequest): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'coordinator' && $user->id === $aidRequest->user_id);
    }
}
