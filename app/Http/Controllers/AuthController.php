<?php

namespace App\Http\Controllers;

use App\Models\User;
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
     * Returns a specific error message depending on what went wrong:
     *  - username not found → "Username tidak ditemukan."
     *  - wrong password     → "Password salah."
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user) {
            return back()
                ->withErrors(['username' => 'Username tidak ditemukan.'])
                ->withInput($request->only('username'));
        }

        if (! Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['password' => 'Password salah.'])
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
