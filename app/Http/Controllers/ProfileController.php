<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil user yang sedang login.
     */
    public function show(): View
    {
        return view('dash.profile.user', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update informasi profil (nama, username, nomor WA, foto).
     */
    public function updateInfo(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash',
                'unique:users,username,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            // Hapus foto lama
            if ($user->avatar) {
                Storage::disk('local')->delete($user->avatar);
            }
            $data['avatar'] = $this->storeAvatar($request->file('avatar'), $user->avatar_directory);
        }

        $user->update($data);

        return redirect()->route('profile.user')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * AJAX: cek ketersediaan username saat edit profil (kecualikan user sendiri).
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $username = trim($request->query('username', ''));

        if (strlen($username) < 3) {
            return response()->json(['available' => true]);
        }

        $exists = User::where('username', $username)
            ->where('id', '!=', auth()->id())
            ->exists();

        return response()->json(['available' => ! $exists]);
    }

    /**
     * AJAX: verifikasi password saat ini milik user yang sedang login.
     */
    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $password = $request->input('current_password', '');

        if (empty($password)) {
            return response()->json(['valid' => false]);
        }

        $isValid = Hash::check($password, auth()->user()->password);

        return response()->json(['valid' => $isValid]);
    }

    /**
     * Ganti password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.user')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Simpan foto profil sebagai WebP terkompresi < 150 KB.
     */
    private function storeAvatar(UploadedFile $file, string $directory): string
    {
        $source = $this->gdCreateFromFile($file->getRealPath(), $file->getMimeType());

        $origW = imagesx($source);
        $origH = imagesy($source);
        $maxW = 400;

        if ($origW > $maxW) {
            $newH = (int) round($origH * ($maxW / $origW));
            $canvas = imagecreatetruecolor($maxW, $newH);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $maxW, $newH, $origW, $origH);
            imagedestroy($source);
            $source = $canvas;
        }

        $quality = 80;
        $targetBytes = 150 * 1024;

        do {
            ob_start();
            imagewebp($source, null, $quality);
            $webpData = ob_get_clean();
            $quality -= 5;
        } while (strlen($webpData) > $targetBytes && $quality >= 10);

        imagedestroy($source);

        $filename = Str::uuid().'.webp';
        $storagePath = $directory.'/'.$filename;

        Storage::disk('local')->put($storagePath, $webpData);

        return $storagePath;
    }

    private function gdCreateFromFile(string $path, string $mime): \GdImage
    {
        return match (true) {
            str_contains($mime, 'png') => imagecreatefrompng($path),
            str_contains($mime, 'webp') => imagecreatefromwebp($path),
            default => imagecreatefromjpeg($path),
        };
    }
}
