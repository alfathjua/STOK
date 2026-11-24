<?php

namespace App\Http\Controllers;

use App\Models\DetailStock;
use App\Models\Receipt;
use App\Models\Stock;
use App\Models\StockIn;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StockInController extends Controller
{
    /**
     * List all stock-in entries, newest first.
     */
    public function index(): View
    {
        $stockIns = StockIn::with(['user', 'details', 'receipts'])
            ->latest()
            ->paginate(20);

        return view('dash.stock-in.index', compact('stockIns'));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        return view('dash.stock-in.action');
    }

    /**
     * Persist a new stock-in record with its details and receipts.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'stock_origin' => ['required', 'string', 'max:255'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'receipts.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($request): void {
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $proofPath = $this->compressAndStore($request->file('proof'), 'proof');
            }

            /** @var StockIn $stockIn */
            $stockIn = StockIn::create([
                'user_id' => auth()->id(),
                'stock_origin' => $request->stock_origin,
                'proof' => $proofPath,
            ]);

            $items = collect($request->items)->map(fn (array $item) => new DetailStock([
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? '',
                'qty' => (int) $item['qty'],
                'price' => (float) $item['price'],
            ]));

            $stockIn->details()->saveMany($items);

            $this->syncStocksOnCreate($stockIn->fresh(['details']));

            if ($request->hasFile('receipts')) {
                foreach ($request->file('receipts') as $file) {
                    Receipt::create([
                        'stock_id' => $stockIn->id,
                        'images' => $this->compressAndStore($file, 'receipts'),
                    ]);
                }
            }
        });

        ActivityLogger::create('Stok Masuk', json_encode([
            'summary' => 'Menambahkan stok masuk baru dari '.$request->stock_origin,
            'origin' => $request->stock_origin,
            'items' => collect($request->items)->map(fn (array $item) => [
                'name' => $item['item_name'],
                'qty' => (int) $item['qty'],
                'price' => (float) $item['price'],
            ])->values()->all(),
        ]));

        return redirect()->route('stock-in.index')
            ->with('success', 'Stok masuk berhasil disimpan.');
    }

    /**
     * Show the edit form for the given stock-in.
     */
    public function edit(StockIn $stockIn): View
    {
        $stockIn->load(['details', 'receipts']);

        return view('dash.stock-in.action', compact('stockIn'));
    }

    /**
     * Update an existing stock-in record.
     */
    public function update(Request $request, StockIn $stockIn): RedirectResponse
    {
        $request->validate([
            'stock_origin' => ['required', 'string', 'max:255'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'receipts.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $stockIn): void {
            $data = ['stock_origin' => $request->stock_origin];

            if ($request->hasFile('proof')) {
                if ($stockIn->proof) {
                    Storage::disk('local')->delete($stockIn->proof);
                }
                $data['proof'] = $this->compressAndStore($request->file('proof'), 'proof');
            }

            $stockIn->update($data);

            // Simpan snapshot qty lama (item_name → qty) sebelum dihapus
            $oldDetails = $stockIn->details()->get(['item_name', 'qty'])
                ->keyBy('item_name');

            $stockIn->details()->delete();

            $items = collect($request->items)->map(fn (array $item) => new DetailStock([
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? '',
                'qty' => (int) $item['qty'],
                'price' => (float) $item['price'],
            ]));

            $stockIn->details()->saveMany($items);

            $this->syncStocksOnUpdate($stockIn->fresh(['details']), $oldDetails);

            if ($request->hasFile('receipts')) {
                foreach ($request->file('receipts') as $file) {
                    Receipt::create([
                        'stock_id' => $stockIn->id,
                        'images' => $this->compressAndStore($file, 'receipts'),
                    ]);
                }
            }
        });

        ActivityLogger::edit('Stok Masuk', json_encode([
            'summary' => 'Memperbarui data stok masuk dari '.$request->stock_origin,
            'origin' => $request->stock_origin,
            'items' => collect($request->items)->map(fn (array $item) => [
                'name' => $item['item_name'],
                'qty' => (int) $item['qty'],
                'price' => (float) $item['price'],
            ])->values()->all(),
        ]));

        return redirect()->route('stock-in.index')
            ->with('success', 'Stok masuk berhasil diperbarui.');
    }

    /**
     * Delete a stock-in record and all its associated files.
     */
    public function destroy(StockIn $stockIn): RedirectResponse
    {
        // Ambil snapshot detail sebelum dihapus untuk log
        $stockIn->load('details');
        $deletedOrigin = $stockIn->stock_origin;
        $deletedItems = $stockIn->details->map(fn (DetailStock $d) => [
            'name' => $d->item_name,
            'qty' => $d->qty,
            'price' => (float) $d->price,
        ])->values()->all();

        DB::transaction(function () use ($stockIn): void {
            if ($stockIn->proof) {
                Storage::disk('local')->delete($stockIn->proof);
            }

            foreach ($stockIn->receipts as $receipt) {
                Storage::disk('local')->delete($receipt->images);
            }

            $stockIn->delete();
        });

        ActivityLogger::delete('Stok Masuk', json_encode([
            'summary' => 'Menghapus data stok masuk dari '.$deletedOrigin,
            'origin' => $deletedOrigin,
            'items' => $deletedItems,
        ]));

        return redirect()->route('stock-in.index')
            ->with('success', 'Stok masuk berhasil dihapus.');
    }

    /**
     * Delete a single receipt image via AJAX.
     */
    public function destroyReceipt(Receipt $receipt): JsonResponse
    {
        Storage::disk('local')->delete($receipt->images);
        $receipt->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Saat stok masuk BARU dibuat: buat record stocks untuk setiap detail
     * dengan date = hari ini, qty_in = qty detail, qty_remaining = qty detail.
     */
    private function syncStocksOnCreate(StockIn $stockIn): void
    {
        $today = now()->toDateString();

        foreach ($stockIn->details as $detail) {
            Stock::updateOrCreate(
                ['detail_stock_id' => $detail->id, 'date' => $today],
                ['qty_in' => $detail->qty, 'qty_remaining' => $detail->qty]
            );
        }
    }

    /**
     * Saat stok masuk di-UPDATE: sesuaikan qty_remaining di record stocks hari ini
     * sebesar selisih qty baru vs qty lama.
     *
     * Contoh: stok lama masuk 400, sisa user di kelola stok = 20.
     * User edit stok masuk jadi 500 (+100). Maka qty_remaining += 100 → 120.
     * Jika dikurangi jadi 300 (-100). Maka qty_remaining -= 100 → max(0, 20-100) = 0.
     *
     * @param  Collection<string,DetailStock>  $oldDetails  keyed by item_name
     */
    private function syncStocksOnUpdate(StockIn $stockIn, Collection $oldDetails): void
    {
        $today = now()->toDateString();

        foreach ($stockIn->details as $detail) {
            $oldQty = (int) ($oldDetails->get($detail->item_name)?->qty ?? 0);
            $newQty = $detail->qty;
            $diff = $newQty - $oldQty;

            $stock = Stock::where('detail_stock_id', $detail->id)
                ->where('date', $today)
                ->first();

            if ($stock) {
                // Adjust qty_in ke nilai baru; adjust qty_remaining by diff
                $stock->qty_in = $newQty;
                $stock->qty_remaining = max(0, $stock->qty_remaining + $diff);
                $stock->save();
            } else {
                // Belum ada record hari ini — buat baru
                Stock::create([
                    'detail_stock_id' => $detail->id,
                    'date' => $today,
                    'qty_in' => $newQty,
                    'qty_remaining' => max(0, $newQty),
                ]);
            }
        }
    }

    /**
     * Compress an uploaded image to <100 KB WebP using PHP GD,
     * then store it on the 'local' disk (storage/app/private).
     *
     * Strategy: start at quality 80, step down by 5 until size < 100 KB,
     * but always resize down when image is larger than 1200 px wide first.
     *
     * @param  string  $directory  e.g. 'proof' or 'receipts'
     * @return string Stored path relative to the disk root
     */
    private function compressAndStore(UploadedFile $file, string $directory): string
    {
        $source = $this->gdCreateFromFile($file->getRealPath(), $file->getMimeType());

        // Resize if wider than 1200 px (keep aspect ratio)
        $origW = imagesx($source);
        $origH = imagesy($source);
        $maxW = 1200;

        if ($origW > $maxW) {
            $newH = (int) round($origH * ($maxW / $origW));
            $canvas = imagecreatetruecolor($maxW, $newH);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $maxW, $newH, $origW, $origH);
            imagedestroy($source);
            $source = $canvas;
        }

        // Find quality that yields < 100 KB
        $quality = 80;
        $targetBytes = 100 * 1024; // 100 KB

        do {
            ob_start();
            imagewebp($source, null, $quality);
            $webpData = ob_get_clean();
            $quality -= 5;
        } while (strlen($webpData) > $targetBytes && $quality >= 10);

        imagedestroy($source);

        // Build path and store via Storage
        $filename = Str::uuid().'.webp';
        $storagePath = $directory.'/'.$filename;

        Storage::disk('local')->put($storagePath, $webpData);

        return $storagePath;
    }

    /**
     * Create a GD image resource from any supported source MIME type.
     */
    private function gdCreateFromFile(string $path, string $mime): \GdImage
    {
        return match (true) {
            str_contains($mime, 'png') => imagecreatefrompng($path),
            str_contains($mime, 'webp') => imagecreatefromwebp($path),
            default => imagecreatefromjpeg($path), // jpg / jpeg
        };
    }
}
