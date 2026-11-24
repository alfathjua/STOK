<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockOut;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOutController extends Controller
{
    /**
     * Tampilkan list stok tersedia hari ini untuk dipilih keluar.
     * Logika sama dengan StockController@index — ambil record terbaru
     * per item dengan date <= hari ini, filter hanya yang qty_remaining > 0.
     */
    public function index(): View
    {
        $today = now()->toDateString();

        $stocks = Stock::with(['detailStock.stockIn'])
            ->whereHas('detailStock')
            ->where('date', '<=', $today)
            ->orderByDesc('date')
            ->get()
            ->unique('detail_stock_id')
            ->values()
            ->filter(fn (Stock $s) => $s->qty_remaining > 0);

        return view('dash.stock-out.index', [
            'stocks' => $stocks,
        ]);
    }

    /**
     * Simpan satu transaksi stok keluar.
     * Kurangi qty_remaining di record stocks yang bersangkutan.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:2048'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $logData = null;

        DB::transaction(function () use ($request, &$logData): void {
            /** @var Stock $stock */
            $stock = Stock::with('detailStock')->findOrFail($request->integer('stock_id'));

            // Pastikan stok masih cukup
            $qty = $request->integer('qty');
            if ($qty > $stock->qty_remaining) {
                // Gunakan max yang tersedia agar tidak negatif
                $qty = $stock->qty_remaining;
            }

            StockOut::create([
                'stock_id' => $stock->id,
                'detail_stock_id' => $stock->detail_stock_id,
                'user_id' => auth()->id(),
                'company_name' => $request->company_name,
                'map_url' => $request->map_url ?: null,
                'qty' => $qty,
                'price' => $stock->detailStock->price,
            ]);

            // Kurangi sisa stok
            $stock->decrement('qty_remaining', $qty);

            $logData = [
                'summary' => 'Mengeluarkan '.$qty.' '.$stock->detailStock->item_name.' ke '.$request->company_name,
                'company' => $request->company_name,
                'item_name' => $stock->detailStock->item_name,
                'qty' => $qty,
                'price' => (float) $stock->detailStock->price,
            ];
        });

        ActivityLogger::create('Stok Keluar', json_encode($logData));

        return redirect()->route('stock-out.index')
            ->with('success', 'Stok keluar berhasil dicatat.');
    }

    /**
     * Tampilkan riwayat seluruh transaksi stok keluar, terbaru di atas.
     */
    public function history(Request $request): View
    {
        $search = $request->query('search', '');

        $stockOuts = StockOut::with(['detailStock', 'user'])
            ->when($search !== '', function ($q) use ($search) {
                $numericId = (int) preg_replace('/[^0-9]/', '', $search);
                $q->whereHas('detailStock', function ($d) use ($search, $numericId) {
                    $d->where('item_name', 'like', '%'.$search.'%')
                        ->orWhere(function ($d2) use ($numericId) {
                            if ($numericId > 0) {
                                $d2->where('id', $numericId);
                            } else {
                                $d2->whereRaw('0=1');
                            }
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dash.stock-out.history', compact('stockOuts', 'search'));
    }
}
