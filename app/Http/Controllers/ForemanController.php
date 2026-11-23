<?php

namespace App\Http\Controllers;

use App\Models\ForemanKey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForemanController extends Controller
{
    /**
     * Tampilkan daftar semua foreman beserta kunci terakhirnya.
     */
    public function index(): View
    {
        $foremans = User::where('role', 'foreman')
            ->orderBy('name')
            ->get();

        // Ambil kunci terbaru per foreman untuk ditampilkan di card
        $latestKeys = ForemanKey::whereNotNull('used_by')
            ->whereIn('used_by', $foremans->pluck('id'))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('used_by')
            ->map(fn ($keys) => $keys->first());

        // Kunci aktif yang belum dipakai (untuk ditampilkan di halaman)
        $activeKeys = ForemanKey::where('is_used', false)
            ->whereNull('used_by')
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn (ForemanKey $k) => $k->is_valid);

        return view('dash.foreman.index', [
            'foremans' => $foremans,
            'latestKeys' => $latestKeys,
            'activeKeys' => $activeKeys,
        ]);
    }

    /**
     * Generate kunci baru 6 karakter (huruf kapital + angka).
     * Dikembalikan sebagai JSON agar bisa ditampilkan langsung di halaman
     * tanpa full page reload.
     */
    public function generateKey(Request $request): JsonResponse
    {
        // Pastikan unik — coba maksimal 10x
        $key = null;
        for ($i = 0; $i < 10; $i++) {
            $candidate = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
            if (! ForemanKey::where('key', $candidate)->exists()) {
                $key = $candidate;
                break;
            }
        }

        if (! $key) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat kunci. Coba lagi.'], 500);
        }

        $foremanKey = ForemanKey::create([
            'key' => $key,
            'is_used' => false,
            'used_by' => null,
        ]);

        return response()->json([
            'success' => true,
            'key' => $foremanKey->key,
            'expires_in' => $foremanKey->expires_in,
            'created_at' => $foremanKey->created_at->translatedFormat('d M Y, H:i'),
        ]);
    }
}
