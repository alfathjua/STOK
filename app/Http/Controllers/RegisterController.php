<?php

namespace App\Http\Controllers;

use App\Models\ForemanKey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan form register foreman.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses register foreman.
     * Validasi kunci, buat user, tandai kunci sebagai terpakai, lalu login.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'size:6'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash',
                'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        // Cari kunci yang valid
        $foremanKey = ForemanKey::where('key', strtoupper($request->key))
            ->where('is_used', false)
            ->first();

        if (! $foremanKey) {
            return back()
                ->withErrors(['key' => 'Kunci tidak ditemukan atau sudah pernah digunakan.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        if ($foremanKey->is_expired) {
            return back()
                ->withErrors(['key' => 'Kunci sudah kedaluwarsa (lebih dari 24 jam). Minta kunci baru dari direktur.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Buat user foreman — role diset secara eksplisit, bukan via mass assignment
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username.'@foreman.local', // placeholder, tidak dipakai login
            'password' => Hash::make($request->password),
        ]);

        $user->role = 'foreman';
        $user->save();

        // Tandai kunci sebagai terpakai
        $foremanKey->update([
            'is_used' => true,
            'used_by' => $user->id,
        ]);

        // Login langsung
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * AJAX: cek apakah username foreman sudah dipakai.
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $username = $request->query('username', '');

        if (strlen($username) < 3) {
            return response()->json(['available' => true]);
        }

        $exists = User::where('username', $username)
            ->where('role', 'foreman')
            ->exists();

        return response()->json(['available' => ! $exists]);
    }
}
