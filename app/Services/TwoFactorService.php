<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Support\Facades\Hash;

class TwoFactorService
{
    public function issue(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        $user->notify(new TwoFactorCodeNotification($code));
    }

    public function verify(User $user, string $code): bool
    {
        if (! $user->two_factor_code || ! $user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $user->two_factor_code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return true;
    }
}
