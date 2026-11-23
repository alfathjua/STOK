{{-- ══════════════════════════════════════
     MODAL KELUARKAN STOK
     Di-render di luar container rounded agar position:fixed
     tidak terjebak dalam stacking context parent.
══════════════════════════════════════ --}}
<div id="outStockModal"
     class="fixed inset-0 z-50 bg-black/60 p-4"
     style="display:none; align-items:center; justify-content:center;"
     onclick="closeOutModalOnBackdrop(event)">

    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div class="min-w-0 pr-3">
                <h3 class="text-sm font-semibold text-slate-800">Keluarkan Stok</h3>
                <p id="outModalItemName" class="text-xs text-slate-400 mt-0.5 truncate"></p>
            </div>
            <button type="button"
                    onclick="closeOutModal()"
                    style="flex-shrink:0; background:transparent; border:none; cursor:pointer; padding:4px; border-radius:6px;"
                    class="text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="outStockForm" method="POST" action="{{ route('stock-out.store') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" id="outStockId" name="stock_id">

            {{-- Nama perusahaan penerima --}}
            <div>
                <label for="outCompanyName" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nama Perusahaan Penerima <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="outCompanyName"
                       name="company_name"
                       placeholder="Contoh: PT. Maju Jaya Konstruksi"
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-800
                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                              focus:border-blue-500 transition">
            </div>

            {{-- Link lokasi / Google Maps --}}
            <div>
                <label for="outMapUrl" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Link Lokasi (Google Maps)
                    <span class="text-slate-400 font-normal">— opsional</span>
                </label>
                <input type="url"
                       id="outMapUrl"
                       name="map_url"
                       placeholder="https://maps.google.com/..."
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-800
                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                              focus:border-blue-500 transition">
                <p class="text-xs text-slate-400 mt-1">Tempel link dari Google Maps agar mudah dilacak.</p>
            </div>

            {{-- Jumlah yang dikeluarkan --}}
            <div>
                <label for="outQtyInput" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Jumlah Dikeluarkan <span class="text-red-500">*</span>
                </label>
                <div style="display:flex; align-items:stretch; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                    <button type="button"
                            onclick="adjustOutQty(-1)"
                            style="width:48px; flex-shrink:0; background:#f8fafc; border:none; border-right:1px solid #cbd5e1;
                                   cursor:pointer; font-size:1.25rem; font-weight:700; color:#475569; line-height:1;
                                   display:flex; align-items:center; justify-content:center;">
                        −
                    </button>
                    <input type="number"
                           id="outQtyInput"
                           name="qty"
                           min="1"
                           style="flex:1; text-align:center; font-size:1.25rem; font-weight:700; padding:0.65rem 0.5rem;
                                  border:none; outline:none; background:#fff; color:#1e293b;
                                  -moz-appearance:textfield; appearance:textfield;">
                    <button type="button"
                            onclick="adjustOutQty(1)"
                            style="width:48px; flex-shrink:0; background:#f8fafc; border:none; border-left:1px solid #cbd5e1;
                                   cursor:pointer; font-size:1.25rem; font-weight:700; color:#475569; line-height:1;
                                   display:flex; align-items:center; justify-content:center;">
                        +
                    </button>
                </div>
                <p id="outQtyHint" class="text-xs text-slate-400 mt-1">Maks. tersedia: <span id="outMaxQty"></span></p>
            </div>

            {{-- Preview total nilai --}}
            <div class="bg-slate-50 rounded-lg px-4 py-3 flex items-center justify-between text-sm">
                <span class="text-slate-500">Estimasi nilai keluar:</span>
                <span id="outTotalValue" class="font-semibold text-slate-800">Rp 0</span>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2.5 rounded-lg transition text-sm">
                    Simpan Keluar
                </button>
                <button type="button"
                        onclick="closeOutModal()"
                        class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium py-2.5 rounded-lg transition text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #outQtyInput::-webkit-outer-spin-button,
    #outQtyInput::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
(function () {
    const modal   = document.getElementById('outStockModal');
    const stockId = document.getElementById('outStockId');
    const nameEl  = document.getElementById('outModalItemName');
    const qtyEl   = document.getElementById('outQtyInput');
    const maxEl   = document.getElementById('outMaxQty');
    const totalEl = document.getElementById('outTotalValue');

    let maxQty   = 0;
    let unitPrice = 0;

    window.openOutModal = function (id, itemName, remaining, price) {
        stockId.value      = id;
        nameEl.textContent = itemName;
        maxQty             = remaining;
        unitPrice          = price;
        maxEl.textContent  = remaining;
        qtyEl.value        = 1;
        updateTotal();

        // Reset field input lainnya
        document.getElementById('outCompanyName').value = '';
        document.getElementById('outMapUrl').value      = '';

        modal.style.display = 'flex';
        setTimeout(function () {
            document.getElementById('outCompanyName').focus();
        }, 50);
    };

    window.closeOutModal = function () {
        modal.style.display = 'none';
    };

    window.closeOutModalOnBackdrop = function (e) {
        if (e.target === modal) { closeOutModal(); }
    };

    window.adjustOutQty = function (delta) {
        const current = parseInt(qtyEl.value, 10) || 1;
        qtyEl.value   = Math.min(maxQty, Math.max(1, current + delta));
        updateTotal();
    };

    function updateTotal() {
        const qty   = parseInt(qtyEl.value, 10) || 0;
        const total = qty * unitPrice;
        totalEl.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    }

    qtyEl.addEventListener('input', function () {
        // Jangan boleh melebihi stok tersedia
        const val = parseInt(qtyEl.value, 10);
        if (val > maxQty) { qtyEl.value = maxQty; }
        if (val < 1)      { qtyEl.value = 1; }
        updateTotal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') {
            closeOutModal();
        }
    });
})();
</script>
