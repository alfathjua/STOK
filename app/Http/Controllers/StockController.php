<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
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
