<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLogin()
    {
        // إذا كان المستخدم مسجل دخوله بالفعل، وجهه فوراً للوحة التحكم
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your registered email address.',
            'email.email' => 'Please enter a valid email format.',
            'password.required' => 'Please enter your password.',
        ]);

        // محاولة التحقق وتسجيل الدخول الآمن
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // إرجاع خطأ في حال عدم مطابقة البيانات المسجلة
        return back()->withErrors([
            'email' => 'The provided credentials do not match our database records.',
        ])->onlyInput('email');
    }

    /**
     * تسجيل الخروج الآمن وتدمير الجلسة
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}