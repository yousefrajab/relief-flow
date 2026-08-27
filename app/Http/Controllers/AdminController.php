<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function users(): View
    {
        $pendingUsers = User::where('status', 'pending_verification')->orderBy('created_at')->get();
        $allUsers = User::where('role', '!=', 'admin')->orderBy('name')->get();

        return view('admin.users', compact('pendingUsers', 'allUsers'));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return redirect()->route('admin.users')->with('success', __('Account for :name has been approved.', ['name' => $user->name]));
    }

    public function reject(User $user): RedirectResponse
    {
        $user->update(['status' => 'suspended']);

        return redirect()->route('admin.users')->with('success', __('Account for :name has been suspended.', ['name' => $user->name]));
    }
}
