<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Splash / opening page shown for 5 seconds before redirecting to login.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('index');
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate the user with username + password.
     *
     * Pesan error sengaja dibuat generik untuk mencegah username enumeration.
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['login' => 'Username atau password salah.'])
                ->withInput($request->only('username'));
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
