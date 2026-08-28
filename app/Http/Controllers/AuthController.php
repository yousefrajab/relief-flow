<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request, TwoFactorService $twoFactor)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => __('These credentials do not match our records.'),
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user->role === 'admin') {
            $twoFactor->issue($user);

            $request->session()->put('two_factor_user_id', $user->id);
            $request->session()->put('two_factor_remember', $request->boolean('remember'));

            return redirect()->route('two-factor.show');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
