<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * Pesan error dibedakan sesuai penyebab kegagalan:
     * - field kosong       → "Username wajib diisi." / "Password wajib diisi."
     * - username tidak ada → "Username tidak ditemukan."
     * - password salah     → "Password salah."
     *
     * Semua pesan dikirim lewat key 'login' agar tampil di alert yang sama.
     */
    public function authenticate(Request $request)
    {
        $username = trim((string) $request->input('username', ''));
        $password = (string) $request->input('password', '');

        if ($username === '' && $password === '') {
            return $this->loginFailed('Username dan password wajib diisi.', $username);
        }

        if ($username === '') {
            return $this->loginFailed('Username wajib diisi.', $username);
        }

        if ($password === '') {
            return $this->loginFailed('Password wajib diisi.', $username);
        }

        $user = User::where('username', $username)->first();

        if (! $user) {
            return $this->loginFailed('Username tidak ditemukan.', $username);
        }

        if (! Hash::check($password, $user->password)) {
            return $this->loginFailed('Password salah.', $username);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Kembali ke form login dengan pesan error dan username yang tadi diisi.
     */
    private function loginFailed(string $message, string $username)
    {
        return back()
            ->withErrors(['login' => $message])
            ->withInput(['username' => $username]);
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