<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function approve(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return redirect()->route('dashboard')->with('success', __('Account for :name has been approved.', ['name' => $user->name]));
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update(['status' => 'suspended']);

        return redirect()->route('dashboard')->with('success', __('Account for :name has been suspended.', ['name' => $user->name]));
    }
}
