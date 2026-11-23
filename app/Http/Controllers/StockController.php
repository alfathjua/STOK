<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Tampilkan daftar stok gudang berdasarkan tanggal.
     *
     * - Default: hari ini
     * - User bisa filter ke tanggal sebelumnya, tidak bisa ke masa depan
     * - Stok dari hari-hari sebelumnya yang belum nol "dibawa" ke tanggal baru
     *   dengan cara menampilkan record stocks terdekat per detail_stock_id
     *   yang date-nya <= tanggal yang dipilih
     */
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        // Validasi tanggal: tidak boleh lebih dari hari ini
        $selectedDate = $request->query('date', $today);
        if ($selectedDate > $today) {
            $selectedDate = $today;
        }

        // Ambil semua detail_stock_id yang punya setidaknya satu record stocks
        // dengan date <= selectedDate, lalu ambil record terbaru per item
        $stocks = Stock::with(['detailStock'])
            ->whereHas('detailStock')
            ->where('date', '<=', $selectedDate)
            ->orderByDesc('date')
            ->get()
            // Ambil satu record terbaru per detail_stock_id
            ->unique('detail_stock_id')
            ->values();

        return view('dash.stock.index', [
            'stocks' => $stocks,
            'selectedDate' => $selectedDate,
            'today' => $today,
        ]);
    }

    /**
     * Update qty_remaining stok (hanya field ini yang bisa diubah user).
     */
    public function update(Request $request, Stock $stock): RedirectResponse
    {
        $request->validate([
            'qty_remaining' => ['required', 'integer', 'min:0'],
        ]);

        $selectedDate = $request->query('date', now()->toDateString());

        // Jika record ini bukan milik tanggal yang dipilih, buat record baru
        // untuk tanggal hari ini dengan data qty_remaining yang baru
        $today = now()->toDateString();

        if ($stock->date->toDateString() !== $today) {
            // Buat/update record untuk hari ini berdasarkan item yang sama
            $todayStock = Stock::updateOrCreate(
                [
                    'detail_stock_id' => $stock->detail_stock_id,
                    'date' => $today,
                ],
                [
                    'qty_in' => $stock->qty_in,
                    'qty_remaining' => $request->integer('qty_remaining'),
                ]
            );
        } else {
            $stock->update([
                'qty_remaining' => $request->integer('qty_remaining'),
            ]);
        }

        return redirect()->route('stock.index', ['date' => $selectedDate])
            ->with('success', 'Stok berhasil diperbarui.');
    }
}
