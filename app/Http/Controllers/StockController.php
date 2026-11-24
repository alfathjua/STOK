<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Tampilkan daftar stok gudang berdasarkan tanggal.
     *
     * - Default: hari ini
     * - User bisa filter ke tanggal sebelumnya, tidak bisa ke masa depan
     * - Menampilkan record stocks terbaru per item dengan date <= selectedDate
     */
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        $selectedDate = $request->query('date', $today);
        if ($selectedDate > $today) {
            $selectedDate = $today;
        }

        $stocks = Stock::with(['detailStock.stockIn'])
            ->whereHas('detailStock')
            ->where('date', '<=', $selectedDate)
            ->orderByDesc('date')
            ->get()
            ->unique('detail_stock_id')
            ->values();

        return view('dash.stock.index', [
            'stocks' => $stocks,
            'selectedDate' => $selectedDate,
            'today' => $today,
        ]);
    }
}
