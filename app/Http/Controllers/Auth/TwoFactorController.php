<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verify(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = $request->session()->get('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $limiterKey = 'two-factor:'.$userId;

        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            return back()->withErrors([
                'code' => __('Too many attempts. Please request a new code.'),
            ]);
        }

        $user = User::findOrFail($userId);

        if (! $twoFactor->verify($user, $request->string('code')->toString())) {
            RateLimiter::hit($limiterKey, 600);

            return back()->withErrors([
                'code' => __('That code is incorrect or has expired.'),
            ]);
        }

        RateLimiter::clear($limiterKey);

        $remember = $request->session()->pull('two_factor_remember', false);
        $request->session()->forget('two_factor_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $userId = $request->session()->get('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $limiterKey = 'two-factor-resend:'.$userId;

        if (RateLimiter::tooManyAttempts($limiterKey, 1)) {
            return back()->withErrors([
                'code' => __('Please wait a minute before requesting another code.'),
            ]);
        }

        RateLimiter::hit($limiterKey, 60);

        $twoFactor->issue(User::findOrFail($userId));

        return back()->with('success', __('A new code has been sent to your email.'));
    }
}
