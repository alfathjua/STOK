@extends('dash.layouts.app', ['title' => isset($stockIn) ? 'Edit Stok Masuk' : 'Tambah Stok Masuk'])

@section('content')

    @php
        $isEdit = isset($stockIn);
        $action = $isEdit ? route('stock-in.update', $stockIn) : route('stock-in.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <div class="max-w-3xl mx-auto">

        <div class="flex items-center gap-3 mb-5">
            <a href="{{ route('stock-in.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
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

            <div id="proofSection" class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-1">
                    Foto Bukti Pembelian <span class="text-red-500">*</span>
                </h3>
                <p class="text-xs text-slate-400 mb-4">Potret struk / nota saat transaksi di toko.</p>
                <input type="file" id="proofInput" name="proof" accept="image/*" class="hidden"
                    onchange="onProofFileSelected(this)">
                <p id="proofError" class="hidden text-red-500 text-xs mb-3">Foto bukti pembelian wajib diunggah.</p>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" onclick="document.getElementById('proofInput').click()"
                        class="inline-flex items-center gap-2 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        Galeri
                    </button>
                    <button type="button" onclick="openCamera('proof')"
                        class="inline-flex items-center gap-2 border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        Potret Pembelian
                    </button>
                    <div id="proofPreviewWrap" style="display:none" class="relative inline-block">
                        <img id="proofPreview" src="" alt="Preview"
                            class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                        <button type="button" onclick="clearProof()"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition">
                            ✕
                        </button>
                    </div>

                    @if ($isEdit && $stockIn->proof)
                        <div id="proofExistingWrap" class="relative inline-block">
                            <img src="{{ route('private.file', ['path' => $stockIn->proof]) }}" alt="Bukti pembelian"
                                class="h-16 w-16 object-cover rounded-lg border border-slate-200">
                            <button type="button" onclick="deleteProofPreview()"
                                class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition">
                                ✕
                            </button>
                            <span class="absolute -bottom-5 left-0 text-xs text-slate-500 whitespace-nowrap">Ganti dengan
                                potret baru</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">Data Barang</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tambah satu atau beberapa jenis barang.</p>
                    </div>
                    <button type="button" onclick="addItem()"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 border border-blue-200 hover:border-blue-400 px-3 py-1.5 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                <div
                    class="hidden sm:grid grid-cols-[1fr_2fr_1fr_1.5fr_1.5fr_auto] gap-2 mb-2 text-xs font-medium text-slate-400 px-1">
                    <span>Kode Barang</span>
                    <span>Nama Barang</span>
                    <span>Qty</span>
                    <span>Harga Satuan (Rp)</span>
                    <span>Total (Rp)</span>
                    <span></span>
                </div>

                <div id="itemRows" class="space-y-2">
                    @if ($isEdit && $stockIn->details->count())
                        @foreach ($stockIn->details as $i => $detail)
                            <div
                                class="item-row grid grid-cols-1 sm:grid-cols-[1fr_2fr_1fr_1.5fr_1.5fr_auto] gap-2 items-start">
                                <div>
                                    <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Kode Barang</label>
                                    <input type="text" readonly value="{{ $detail->item_code }}"
                                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-500 font-mono cursor-default">
                                </div>
                                <div>
                                    <label class="sm:hidden text-xs text-slate-400 mb-0.5 block">Nama Barang</label>
                                    <input type="text" name="items[{{ $i }}][item_name]"
                                        value="{{ $detail->item_name }}" placeholder="Nama barang"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                                    <input type="hidden" name="items[{{ $i }}][description]"
                                        value="{{ $detail->description }}">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
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
                                <input type="number" name="items[0][price]" min="0" step="1"
                                    placeholder="0"
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
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

            <div id="receiptSection" class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-1">
                    Foto Struk Barang <span class="text-red-500">*</span>
                </h3>
                <p class="text-xs text-slate-400 mb-1">
                    Foto struk / bukti pembelian per item. Bisa lebih dari satu. Maks. 2 MB per file.
                </p>

                <p id="receiptError" class="hidden text-red-500 text-xs mb-3">Minimal satu foto struk wajib diunggah.</p>
                @if ($isEdit && $stockIn->receipts->count())
                    <div id="existingReceiptsWrap" class="flex flex-wrap gap-2 mb-4">
                        @foreach ($stockIn->receipts as $receipt)
                            <div id="receipt-{{ $receipt->id }}" class="receipt-item relative group">
                                <img src="{{ route('private.file', ['path' => $receipt->images]) }}" alt="Struk"
                                    class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                <span
                                    class="absolute -top-1 -right-1 w-4 h-4 bg-slate-500 text-white text-[9px] flex items-center justify-center rounded-full">
                                    {{ $loop->iteration }}
                                </span>
                                <button type="button" onclick="deleteReceiptAjax({{ $receipt->id }})"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition sm:opacity-0 sm:group-hover:opacity-100 opacity-100">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                        <p class="w-full text-xs text-slate-400 mt-1">
                            Struk tersimpan ({{ $stockIn->receipts->count() }}). Tambah foto baru akan ditambahkan. <span
                                class="hidden sm:inline">Hover</span><span class="sm:hidden">Tekan lama</span> untuk
                            hapus.
                        </p>
                    </div>
                @elseif ($isEdit)
                    <p class="text-xs text-slate-400 mb-4">Belum ada struk tersimpan. Upload minimal satu struk.</p>
                @endif

                <div id="receiptPreviews" class="flex flex-wrap gap-2 mb-4"></div>
                <input type="file" id="receiptGallery" accept="image/*" multiple class="hidden"
                    onchange="addReceiptFromGallery(this)">
                <div id="receiptFileInputs" class="hidden"></div>

                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="document.getElementById('receiptGallery').click()"
                        class="inline-flex items-center gap-2 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        Galeri
                    </button>

                    <button type="button" onclick="openCamera('receipt')"
                        class="inline-flex items-center gap-2 border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        Potret
                    </button>
                </div>
            </div>
            <div class="flex gap-3 pb-6">
                <button type="button" onclick="submitForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition">
                    {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                </button>
                <a href="{{ route('stock-in.index') }}"
                    class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-6 py-2.5 rounded-lg transition">
                    Batal
                </a>
            </div>

        </form>

        <div id="validationAlert" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 bg-red-50 border-b border-red-100">
                    <div class="flex-shrink-0 w-9 h-9 bg-red-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-red-800">Form Belum Lengkap</h3>
                </div>
                <div class="px-5 py-4">
                    <ul id="validationMessages" class="text-sm text-slate-700 space-y-1.5 list-disc list-inside"></ul>
                </div>
                <div class="px-5 pb-5">
                    <button type="button" onclick="closeValidationAlert()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                        Perbaiki Isian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="cameraModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl overflow-hidden">

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 id="cameraModalTitle" class="text-sm font-semibold text-slate-800">Potret</h3>
                <button type="button" onclick="closeCamera()" class="text-slate-400 hover:text-slate-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-4 space-y-3">

                <div id="viewfinderWrap" class="relative bg-black rounded-xl overflow-hidden" style="aspect-ratio:4/3">
                    <video id="cameraStream" autoplay playsinline muted class="w-full h-full object-cover"></video>
                </div>

                <div id="capturedWrap" class="hidden">
                    <img id="capturedPreview" src="#" alt="Hasil potret"
                        class="w-full rounded-xl border border-slate-200 object-cover">
                </div>

                <div id="shootBtns">
                    <button type="button" onclick="shootPhoto()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                        Ambil Foto
                    </button>
                </div>
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

    <canvas id="cameraCanvas" class="hidden"></canvas>

    @push('head')
        <style>
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                opacity: 1;
            }
        </style>
    @endpush

    <script>
        function submitForm() {
            const errors = [];
            const stockOrigin = document.getElementById('stock_origin');
            if (!stockOrigin.value.trim()) {
                errors.push('Nama Toko / Supplier wajib diisi.');
                stockOrigin.classList.add('border-red-400');
                stockOrigin.classList.remove('border-slate-300');
            } else {
                stockOrigin.classList.remove('border-red-400');
                stockOrigin.classList.add('border-slate-300');
            }

            const proofInput = document.getElementById('proofInput');
            const proofExistingWrap = document.getElementById('proofExistingWrap');
            const hasProofNew = proofInput && proofInput.files && proofInput.files.length > 0;
            const hasProofExisting = proofExistingWrap !== null;
            const proofErrorEl = document.getElementById('proofError');
            const proofSection = document.getElementById('proofSection');

            if (!hasProofNew && !hasProofExisting) {
                errors.push('Foto Bukti Pembelian wajib diunggah.');
                proofErrorEl.classList.remove('hidden');
                proofSection.classList.add('border-red-300');
                proofSection.classList.remove('border-slate-200');
            } else {
                proofErrorEl.classList.add('hidden');
                proofSection.classList.remove('border-red-300');
                proofSection.classList.add('border-slate-200');
            }
            const existingReceipts = document.querySelectorAll('.receipt-item');
            const newReceiptInputs = document.querySelectorAll('#receiptFileInputs input[type="file"]');
            const hasReceiptNew = newReceiptInputs.length > 0;
            const hasReceiptExisting = existingReceipts.length > 0;
            const receiptErrorEl = document.getElementById('receiptError');
            const receiptSection = document.getElementById('receiptSection');

            if (!hasReceiptNew && !hasReceiptExisting) {
                errors.push('Foto Struk Barang wajib diunggah minimal satu.');
                receiptErrorEl.classList.remove('hidden');
                receiptSection.classList.add('border-red-300');
                receiptSection.classList.remove('border-slate-200');
            } else {
                receiptErrorEl.classList.add('hidden');
                receiptSection.classList.remove('border-red-300');
                receiptSection.classList.add('border-slate-200');
            }
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, idx) => {
                const nameInput = row.querySelector('input[name*="item_name"]');
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.price-input');
                const rowLabel = `Baris ${idx + 1}`;
                let rowHasError = false;

                if (!nameInput?.value.trim()) {
                    errors.push(`${rowLabel}: Nama Barang wajib diisi.`);
                    nameInput?.classList.add('border-red-400');
                    nameInput?.classList.remove('border-slate-300');
                    rowHasError = true;
                } else {
                    nameInput?.classList.remove('border-red-400');
                    nameInput?.classList.add('border-slate-300');
                }

                if (!qtyInput?.value || parseInt(qtyInput.value) < 1) {
                    errors.push(`${rowLabel}: Qty wajib diisi dan minimal 1.`);
                    qtyInput?.classList.add('border-red-400');
                    qtyInput?.classList.remove('border-slate-300');
                    rowHasError = true;
                } else {
                    qtyInput?.classList.remove('border-red-400');
                    qtyInput?.classList.add('border-slate-300');
                }

                if (!priceInput?.value || parseFloat(priceInput.value) < 0) {
                    errors.push(`${rowLabel}: Harga Satuan wajib diisi.`);
                    priceInput?.classList.add('border-red-400');
                    priceInput?.classList.remove('border-slate-300');
                    rowHasError = true;
                } else {
                    priceInput?.classList.remove('border-red-400');
                    priceInput?.classList.add('border-slate-300');
                }
            });

            if (errors.length > 0) {
                const list = document.getElementById('validationMessages');
                list.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
                document.getElementById('validationAlert').classList.remove('hidden');
                const firstError = document.querySelector('.border-red-400');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return;
            }

            document.getElementById('stockInForm').submit();
        }

        function closeValidationAlert() {
            document.getElementById('validationAlert').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('validationAlert').addEventListener('click', function(e) {
                if (e.target === this) closeValidationAlert();
            });

            document.getElementById('stock_origin').addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-400');
                    this.classList.add('border-slate-300');
                }
            });

            document.getElementById('itemRows').addEventListener('input', function(e) {
                const input = e.target;
                if (input.classList.contains('border-red-400') && input.value.trim()) {
                    input.classList.remove('border-red-400');
                    input.classList.add('border-slate-300');
                }
            });
        });

        let rowIndex = {{ $isEdit ? $stockIn->details->count() : 1 }};

        function addItem() {
            const idx = rowIndex++;
            const wrap = document.getElementById('itemRows');
            const row = document.createElement('div');
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
            const row = input.closest('.item-row');
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            row.querySelector('.total-display').value = formatRp(qty * price);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grand = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                grand += (parseFloat(row.querySelector('.qty-input')?.value) || 0) *
                    (parseFloat(row.querySelector('.price-input')?.value) || 0);
            });
            document.getElementById('grandTotal').textContent = 'Rp ' + formatRp(grand);
        }

        function formatRp(n) {
            return Math.round(n).toLocaleString('id-ID');
        }

        document.addEventListener('DOMContentLoaded', updateGrandTotal);

        function onProofFileSelected(input) {
            if (!input.files || !input.files[0]) {
                return;
            }
            const url = URL.createObjectURL(input.files[0]);
            document.getElementById('proofPreview').src = url;
            document.getElementById('proofPreviewWrap').style.display = 'inline-block';
            document.getElementById('proofError').classList.add('hidden');
            document.getElementById('proofSection').classList.remove('border-red-300');
            document.getElementById('proofSection').classList.add('border-slate-200');
        }

        function clearProof() {
            document.getElementById('proofInput').value = '';
            document.getElementById('proofPreview').src = '';
            document.getElementById('proofPreviewWrap').style.display = 'none';
        }

        function deleteProofPreview() {

            document.getElementById('proofExistingWrap').remove();
        }

        function removeNewReceipt(btn) {
            const wrap = btn.closest('.relative');
            const idx = wrap.dataset.receiptIdx;
            const hiddenInput = document.querySelector(`#receiptFileInputs input[data-idx="${idx}"]`);
            if (hiddenInput) {
                hiddenInput.remove();
            }
            wrap.remove();
            renumberReceiptPreviews();
        }

        function appendReceiptPreview(url, fileIdx) {
            const container = document.getElementById('receiptPreviews');
            const wrap = document.createElement('div');
            wrap.className = 'relative group receipt-preview-item';
            wrap.dataset.receiptIdx = fileIdx;
            const num = container.childElementCount + 1;
            wrap.innerHTML = `
        <img src="${url}" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
        <span class="receipt-num absolute -top-1 -right-1 w-4 h-4 bg-blue-600 text-white text-[9px] flex items-center justify-center rounded-full">${num}</span>
        <button type="button" onclick="removeNewReceipt(this)"
                class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white flex items-center justify-center rounded-full text-xs hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
            ✕
        </button>`;
            container.appendChild(wrap);
            document.getElementById('receiptError').classList.add('hidden');
            document.getElementById('receiptSection').classList.remove('border-red-300');
            document.getElementById('receiptSection').classList.add('border-slate-200');
        }

        function renumberReceiptPreviews() {
            document.querySelectorAll('#receiptPreviews .receipt-preview-item').forEach((el, i) => {
                const badge = el.querySelector('.receipt-num');
                if (badge) {
                    badge.textContent = i + 1;
                }
            });
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
                        if (!document.querySelector('.receipt-item') &&
                            !document.querySelector('#receiptFileInputs input[type="file"]')) {
                            document.getElementById('receiptError').classList.remove('hidden');
                            document.getElementById('receiptSection').classList.add('border-red-300');
                            document.getElementById('receiptSection').classList.remove('border-slate-200');
                        }
                    } else {
                        alert(data.message || 'Gagal menghapus struk.');
                    }
                })
                .catch(() => alert('Terjadi kesalahan saat menghapus struk.'));
        }

        let receiptFileIdx = 0;

        function addFileToForm(file) {
            const idx = receiptFileIdx++;
            const dt = new DataTransfer();
            dt.items.add(file);

            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'receipts[]';
            input.accept = 'image/*';
            input.dataset.idx = idx;
            input.files = dt.files;
            document.getElementById('receiptFileInputs').appendChild(input);

            appendReceiptPreview(URL.createObjectURL(file), idx);
        }

        function addReceiptFromGallery(input) {
            if (!input.files) {
                return;
            }
            Array.from(input.files).forEach(file => addFileToForm(file));
            input.value = '';

            function syncReceiptInput() {}


            let cameraStream = null;
            let cameraContext = 'proof';

            async function openCamera(context) {
                cameraContext = context;
                document.getElementById('cameraModalTitle').textContent =
                    context === 'proof' ? 'Potret Bukti Pembelian' : 'Potret Struk';
                setViewfinderMode();

                document.getElementById('cameraModal').classList.remove('hidden');

                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment'
                        },
                        audio: false,
                    });
                    document.getElementById('cameraStream').srcObject = cameraStream;
                } catch {
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

            function setViewfinderMode() {
                document.getElementById('viewfinderWrap').classList.remove('hidden');
                document.getElementById('capturedWrap').classList.add('hidden');
                document.getElementById('shootBtns').classList.remove('hidden');
                document.getElementById('retakeBtns').classList.add('hidden');
            }

            function setPreviewMode() {
                document.getElementById('viewfinderWrap').classList.add('hidden');
                document.getElementById('capturedWrap').classList.remove('hidden');
                document.getElementById('shootBtns').classList.add('hidden');
                document.getElementById('retakeBtns').classList.remove('hidden');
            }

            function shootPhoto() {
                const video = document.getElementById('cameraStream');
                const canvas = document.getElementById('cameraCanvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                document.getElementById('capturedPreview').src = canvas.toDataURL('image/jpeg', 0.92);
                setPreviewMode();
            }

            function retakePhoto() {
                setViewfinderMode();
            }

            async function confirmPhoto() {
                const canvas = document.getElementById('cameraCanvas');
                const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

                const res = await fetch(dataUrl);
                const blob = await res.blob();

                if (cameraContext === 'proof') {
                    const file = new File([blob], 'proof.jpg', {
                        type: 'image/jpeg'
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('proofInput').files = dt.files;
                    document.getElementById('proofPreview').src = dataUrl;
                    document.getElementById('proofPreviewWrap').style.display = 'inline-block';
                    document.getElementById('proofError').classList.add('hidden');
                    document.getElementById('proofSection').classList.remove('border-red-300');
                    document.getElementById('proofSection').classList.add('border-slate-200');

                } else {
                    const file = new File([blob], `receipt-${Date.now()}.jpg`, {
                        type: 'image/jpeg'
                    });
                    addFileToForm(file);
                }

                closeCamera();
            }
    </script>

@endsection
