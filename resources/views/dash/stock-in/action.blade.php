@extends('dash.layouts.app', ['title' => isset($stockIn) ? 'Edit Stok Masuk' : 'Tambah Stok Masuk'])

@section('content')

@php
    $isEdit = isset($stockIn);
    $action = $isEdit ? route('stock-in.update', $stockIn) : route('stock-in.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('stock-in.index') }}" class="text-slate-400 hover:text-slate-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">
                {{ $isEdit ? 'Edit Stok Masuk' : 'Tambah Stok Masuk' }}
            </h2>
            <p class="text-xs text-slate-500">
                {{ $isEdit ? 'Perbarui data stok masuk yang sudah ada.' : 'Catat pengadaan barang baru.' }}
            </p>
        </div>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <p class="font-medium mb-1">Periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="stockInForm" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method($method)

        {{-- ───── 1. Asal Stok ───── --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Asal Stok</h3>
            <div>
                <label for="stock_origin" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nama Toko / Supplier <span class="text-red-500">*</span>
                </label>
                <input type="text" id="stock_origin" name="stock_origin"
                       value="{{ old('stock_origin', $stockIn->stock_origin ?? '') }}"
                       placeholder="Contoh: Toko Material Maju Jaya"
                       class="w-full px-4 py-2.5 border rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition @error('stock_origin') border-red-400 @else border-slate-300 @enderror">
                @error('stock_origin')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ───── 2. Foto Bukti Pembelian ───── --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">Foto Bukti Pembelian</h3>
            <p class="text-xs text-slate-400 mb-4">Potret struk / nota saat transaksi di toko.</p>

            {{-- Hidden file input — target inject dari modal kamera --}}
            <input type="file" id="proofInput" name="proof" accept="image/*" class="hidden">

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openCamera('proof')"
                        class="inline-flex items-center gap-2 border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                    Potret Pembelian
                </button>

                {{-- Preview foto baru dari potret --}}
                <div id="proofPreviewWrap" class="hidden relative inline-block">
                    <img id="proofPreview" src="#" alt="Preview"
                         class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                    <button type="button" onclick="clearProof()"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition">
                        ✕
                    </button>
                </div>

                {{-- Preview foto lama saat edit --}}
                @if ($isEdit && $stockIn->proof)
                    <div id="proofExistingWrap" class="relative inline-block">
                        <img src="{{ route('private.file', ['path' => $stockIn->proof]) }}"
                             alt="Bukti pembelian"
                             class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                        <button type="button" onclick="deleteProofPreview()"
                                class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition">
                            ✕
                        </button>
                        <span class="absolute -bottom-5 left-0 text-xs text-slate-500 whitespace-nowrap">Ganti dengan potret baru</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ───── 3. Data Barang (multiple) ───── --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700">Data Barang</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tambah satu atau beberapa jenis barang.</p>
                </div>
                <button type="button" onclick="addItem()"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 border border-blue-200 hover:border-blue-400 px-3 py-1.5 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Baris
                </button>
            </div>

            <div class="hidden sm:grid grid-cols-[2fr_1fr_1.5fr_1.5fr_auto] gap-2 mb-2 text-xs font-medium text-slate-400 px-1">
                <span>Nama Barang</span>
                <span>Qty</span>
                <span>Harga Satuan (Rp)</span>
                <span>Total (Rp)</span>
                <span></span>
            </div>

            <div id="itemRows" class="space-y-2">
                @if ($isEdit && $stockIn->details->count())
                    @foreach ($stockIn->details as $i => $detail)
                        <div class="item-row grid grid-cols-1 sm:grid-cols-[2fr_1fr_1.5fr_1.5fr_auto] gap-2 items-start">
                            <div>
                                <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Nama Barang</label>
                                <input type="text" name="items[{{ $i }}][item_name]"
                                       value="{{ $detail->item_name }}" placeholder="Nama barang"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                                <input type="hidden" name="items[{{ $i }}][description]" value="{{ $detail->description }}">
                            </div>
                            <div>
                                <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Qty</label>
                                <input type="number" name="items[{{ $i }}][qty]"
                                       value="{{ $detail->qty }}" min="1" placeholder="1"
                                       class="qty-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                                       oninput="calcTotal(this)">
                            </div>
                            <div>
                                <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Harga Satuan</label>
                                <input type="number" name="items[{{ $i }}][price]"
                                       value="{{ $detail->price }}" min="0" step="1" placeholder="0"
                                       class="price-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                                       oninput="calcTotal(this)">
                            </div>
                            <div>
                                <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Total</label>
                                <input type="text" readonly
                                       value="{{ number_format($detail->qty * $detail->price, 0, ',', '.') }}"
                                       class="total-display w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 cursor-default">
                            </div>
                            <div class="flex items-end pb-0.5">
                                <button type="button" onclick="removeItem(this)"
                                        class="p-2 text-red-400 hover:text-red-600 transition rounded-lg hover:bg-red-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item-row grid grid-cols-1 sm:grid-cols-[2fr_1fr_1.5fr_1.5fr_auto] gap-2 items-start">
                        <div>
                            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Nama Barang</label>
                            <input type="text" name="items[0][item_name]" placeholder="Nama barang"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                            <input type="hidden" name="items[0][description]" value="">
                        </div>
                        <div>
                            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Qty</label>
                            <input type="number" name="items[0][qty]" min="1" placeholder="1"
                                   class="qty-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                                   oninput="calcTotal(this)">
                        </div>
                        <div>
                            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Harga Satuan</label>
                            <input type="number" name="items[0][price]" min="0" step="1" placeholder="0"
                                   class="price-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                                   oninput="calcTotal(this)">
                        </div>
                        <div>
                            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Total</label>
                            <input type="text" readonly value="0"
                                   class="total-display w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 cursor-default">
                        </div>
                        <div class="flex items-end pb-0.5">
                            <button type="button" onclick="removeItem(this)"
                                    class="p-2 text-red-400 hover:text-red-600 transition rounded-lg hover:bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex justify-end">
                <div class="text-sm text-slate-500">
                    Grand Total:
                    <span id="grandTotal" class="ml-2 text-base font-bold text-slate-800">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- ───── 4. Foto Struk Barang ───── --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-1">Foto Struk Barang</h3>
            <p class="text-xs text-slate-400 mb-4">
                Foto struk / bukti pembelian per item. Bisa lebih dari satu. Maks. 2 MB per file.
            </p>

            {{-- Existing receipts (edit mode) --}}
            @if ($isEdit && $stockIn->receipts->count())
                <div id="existingReceiptsWrap" class="flex flex-wrap gap-2 mb-4">
                    @foreach ($stockIn->receipts as $receipt)
                        <div id="receipt-{{ $receipt->id }}" class="receipt-item relative group">
                            <img src="{{ route('private.file', ['path' => $receipt->images]) }}" alt="Struk"
                                 class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-slate-500 text-white text-[9px] flex items-center justify-center rounded-full">
                                {{ $loop->iteration }}
                            </span>
                            <button type="button" onclick="deleteReceiptAjax({{ $receipt->id }})"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
                                ✕
                            </button>
                        </div>
                    @endforeach
                    <p class="w-full text-xs text-slate-400 mt-1">Struk sudah ada. Tambah baru akan ditambahkan. Hover untuk hapus.</p>
                </div>
            @endif

            {{-- Preview area untuk foto baru --}}
            <div id="receiptPreviews" class="flex flex-wrap gap-2 mb-4"></div>

            {{-- Hidden input galeri (multi) --}}
            <input type="file" id="receiptGallery" name="receipts[]"
                   accept="image/*" multiple class="hidden"
                   onchange="addReceiptFromGallery(this)">

            <div class="flex flex-wrap gap-3">
                {{-- Galeri --}}
                <button type="button" onclick="document.getElementById('receiptGallery').click()"
                        class="inline-flex items-center gap-2 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    Galeri
                </button>

                {{-- Potret pakai modal kamera --}}
                <button type="button" onclick="openCamera('receipt')"
                        class="inline-flex items-center gap-2 border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                    Potret
                </button>
            </div>
        </div>

        {{-- Tombol submit --}}
        <div class="flex gap-3 pb-6">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition">
                {{ $isEdit ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('stock-in.index') }}"
               class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg transition">
                Batal
            </a>
        </div>

    </form>
</div>

{{-- ══════════════════════════════════════
     MODAL KAMERA (satu modal, dua konteks)
══════════════════════════════════════ --}}
<div id="cameraModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 id="cameraModalTitle" class="text-sm font-semibold text-slate-800">Potret</h3>
            <button type="button" onclick="closeCamera()"
                    class="text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-3">

            {{-- Viewfinder (video live) --}}
            <div id="viewfinderWrap" class="relative bg-black rounded-xl overflow-hidden" style="aspect-ratio:4/3">
                <video id="cameraStream" autoplay playsinline muted class="w-full h-full object-cover"></video>
            </div>

            {{-- Preview hasil potret (tersembunyi saat viewfinder aktif) --}}
            <div id="capturedWrap" class="hidden">
                <img id="capturedPreview" src="#" alt="Hasil potret"
                     class="w-full rounded-xl border border-slate-200 object-cover">
            </div>

            {{-- Tombol Ambil Foto (muncul saat viewfinder aktif) --}}
            <div id="shootBtns">
                <button type="button" onclick="shootPhoto()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                    Ambil Foto
                </button>
            </div>

            {{-- Tombol Ulangi + Gunakan Foto (muncul setelah potret) --}}
            <div id="retakeBtns" class="hidden flex gap-3">
                <button type="button" onclick="retakePhoto()"
                        class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium py-2.5 rounded-lg transition text-sm">
                    Ulangi
                </button>
                <button type="button" onclick="confirmPhoto()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                    Gunakan Foto
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Canvas tersembunyi untuk capture frame --}}
<canvas id="cameraCanvas" class="hidden"></canvas>

@push('head')
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { opacity: 1; }
</style>
@endpush

<script>
/* ════════════════════════════════════════
   Item rows — dynamic add / remove / total
════════════════════════════════════════ */
let rowIndex = {{ $isEdit ? $stockIn->details->count() : 1 }};

function addItem() {
    const idx  = rowIndex++;
    const wrap = document.getElementById('itemRows');
    const row  = document.createElement('div');
    row.className = 'item-row grid grid-cols-1 sm:grid-cols-[2fr_1fr_1.5fr_1.5fr_auto] gap-2 items-start';
    row.innerHTML = `
        <div>
            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Nama Barang</label>
            <input type="text" name="items[${idx}][item_name]" placeholder="Nama barang"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
            <input type="hidden" name="items[${idx}][description]" value="">
        </div>
        <div>
            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Qty</label>
            <input type="number" name="items[${idx}][qty]" min="1" placeholder="1"
                   class="qty-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                   oninput="calcTotal(this)">
        </div>
        <div>
            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Harga Satuan</label>
            <input type="number" name="items[${idx}][price]" min="0" step="1" placeholder="0"
                   class="price-input w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                   oninput="calcTotal(this)">
        </div>
        <div>
            <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Total</label>
            <input type="text" readonly value="0"
                   class="total-display w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 cursor-default">
        </div>
        <div class="flex items-end pb-0.5">
            <button type="button" onclick="removeItem(this)"
                    class="p-2 text-red-400 hover:text-red-600 transition rounded-lg hover:bg-red-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>`;
    wrap.appendChild(row);
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) return;
    btn.closest('.item-row').remove();
    updateGrandTotal();
}

function calcTotal(input) {
    const row   = input.closest('.item-row');
    const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    row.querySelector('.total-display').value = formatRp(qty * price);
    updateGrandTotal();
}

function updateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        grand += (parseFloat(row.querySelector('.qty-input')?.value)   || 0)
               * (parseFloat(row.querySelector('.price-input')?.value) || 0);
    });
    document.getElementById('grandTotal').textContent = 'Rp ' + formatRp(grand);
}

function formatRp(n) {
    return Math.round(n).toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', updateGrandTotal);

/* ════════════════════════════════════════
   Proof preview helpers
════════════════════════════════════════ */
function clearProof() {
    document.getElementById('proofInput').value = '';
    document.getElementById('proofPreviewWrap').classList.add('hidden');
}

function deleteProofPreview() {
    // Hapus preview proof lama (edit mode) — saat submit, akan di-replace atau kosong
    document.getElementById('proofExistingWrap').remove();
}

function removeNewReceipt(btn) {
    // Hapus preview receipt baru dari input
    btn.closest('.relative').remove();
    syncReceiptInput();
}

function appendReceiptPreview(url) {
    const container = document.getElementById('receiptPreviews');
    const wrap = document.createElement('div');
    wrap.className = 'relative group';
    const idx = container.childElementCount + 1;
    wrap.innerHTML = `
        <img src="${url}" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-blue-600 text-white text-[9px] flex items-center justify-center rounded-full">${idx}</span>
        <button type="button" onclick="removeNewReceipt(this)"
                class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
            ✕
        </button>`;
    container.appendChild(wrap);
}

function deleteReceiptAjax(receiptId) {
    if (!confirm('Hapus struk ini?')) return;

    const elem = document.getElementById(`receipt-${receiptId}`);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(`/receipts/${receiptId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            elem?.remove();
        } else {
            alert(data.message || 'Gagal menghapus struk.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan saat menghapus struk.');
    });
}

/* ════════════════════════════════════════
   Receipt — galeri (multi-file, accumulate via DataTransfer)
════════════════════════════════════════ */
// Accumulate selected files across multiple picks
const receiptFiles = new DataTransfer();

function addReceiptFromGallery(input) {
    if (!input.files) return;
    Array.from(input.files).forEach(file => {
        receiptFiles.items.add(file);
        appendReceiptPreview(URL.createObjectURL(file));
    });
    // Sync hidden input so it carries all selected files on submit
    syncReceiptInput();
    input.value = ''; // reset so the same file can be picked again
}

function syncReceiptInput() {
    // The gallery input already has name="receipts[]", we just keep it in sync
    const galleryInput = document.getElementById('receiptGallery');
    galleryInput.files = receiptFiles.files;
}

/* ════════════════════════════════════════
   Camera modal — shared for proof & receipt
   context: 'proof' | 'receipt'
════════════════════════════════════════ */
let cameraStream  = null;
let cameraContext = 'proof'; // which input to inject into

async function openCamera(context) {
    cameraContext = context;

    // Update modal title
    document.getElementById('cameraModalTitle').textContent =
        context === 'proof' ? 'Potret Bukti Pembelian' : 'Potret Struk';

    // Reset modal state to viewfinder mode
    setViewfinderMode();

    document.getElementById('cameraModal').classList.remove('hidden');

    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            audio: false,
        });
        document.getElementById('cameraStream').srcObject = cameraStream;
    } catch {
        // Browser / device doesn't support camera — close modal gracefully
        closeCamera();
        alert('Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.');
    }
}

function closeCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    document.getElementById('cameraModal').classList.add('hidden');
    setViewfinderMode();
}

/** Show live video, hide preview */
function setViewfinderMode() {
    document.getElementById('viewfinderWrap').classList.remove('hidden');
    document.getElementById('capturedWrap').classList.add('hidden');
    document.getElementById('shootBtns').classList.remove('hidden');
    document.getElementById('retakeBtns').classList.add('hidden');
}

/** Hide live video, show captured preview */
function setPreviewMode() {
    document.getElementById('viewfinderWrap').classList.add('hidden');
    document.getElementById('capturedWrap').classList.remove('hidden');
    document.getElementById('shootBtns').classList.add('hidden');
    document.getElementById('retakeBtns').classList.remove('hidden');
}

function shootPhoto() {
    const video  = document.getElementById('cameraStream');
    const canvas = document.getElementById('cameraCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    // Show captured image, hide viewfinder
    document.getElementById('capturedPreview').src = canvas.toDataURL('image/jpeg', 0.92);
    setPreviewMode();
}

function retakePhoto() {
    // Go back to live viewfinder
    setViewfinderMode();
}

async function confirmPhoto() {
    const canvas  = document.getElementById('cameraCanvas');
    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

    const res  = await fetch(dataUrl);
    const blob = await res.blob();

    if (cameraContext === 'proof') {
        // Inject into proof hidden input
        const file = new File([blob], 'proof.jpg', { type: 'image/jpeg' });
        const dt   = new DataTransfer();
        dt.items.add(file);
        document.getElementById('proofInput').files = dt.files;

        // Show inline preview
        document.getElementById('proofPreview').src = dataUrl;
        document.getElementById('proofPreviewWrap').classList.remove('hidden');

    } else {
        // Append to receipt accumulator
        const file = new File([blob], `receipt-${Date.now()}.jpg`, { type: 'image/jpeg' });
        receiptFiles.items.add(file);
        appendReceiptPreview(dataUrl);
        syncReceiptInput();
    }

    closeCamera();
}
</script>

@endsection
