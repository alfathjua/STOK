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
        <form id="outStockForm" method="POST" action="{{ route('stock-out.store') }}" class="p-5 space-y-4" novalidate>
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
                <p id="outCompanyError" class="hidden text-red-500 text-xs mt-1">Nama perusahaan penerima wajib diisi.</p>
            </div>

            {{-- Link lokasi / Google Maps --}}
            <div>
                <label for="outMapUrl" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Link Lokasi (Google Maps) <span class="text-red-500">*</span>
                </label>
                <input type="url"
                       id="outMapUrl"
                       name="map_url"
                       placeholder="https://maps.google.com/..."
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-800
                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                              focus:border-blue-500 transition">
                <p id="outMapError" class="hidden text-red-500 text-xs mt-1">Link lokasi (Google Maps) wajib diisi.</p>
                <p id="outMapHint" class="text-xs text-slate-400 mt-1">Tempel link dari Google Maps agar mudah dilacak.</p>
            </div>

            {{-- Jumlah yang dikeluarkan --}}
            <div>
                <label for="outQtyInput" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Jumlah Dikeluarkan <span class="text-red-500">*</span>
                </label>
                <div style="position:relative; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                    <input type="number"
                           id="outQtyInput"
                           name="qty"
                           min="1"
                           style="display:block; width:100%; box-sizing:border-box; text-align:center; font-size:1.25rem; font-weight:700;
                                  padding:0.65rem 48px; border:none; outline:none; background:#fff; color:#1e293b;
                                  -moz-appearance:textfield; appearance:textfield;">
                    <button type="button"
                            onclick="adjustOutQty(1)"
                            style="position:absolute; top:0; right:0; bottom:0; width:48px; background:#f8fafc; border:none; border-left:1px solid #cbd5e1;
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

{{-- Alert Modal Validasi (style sama dengan form Stok Masuk) --}}
<div id="outValidationAlert"
     class="fixed inset-0 bg-black/50 p-4"
     style="display:none; align-items:center; justify-content:center; z-index:60;">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 bg-red-50 border-b border-red-100">
            <div class="flex-shrink-0 w-9 h-9 bg-red-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-red-800">Form Belum Lengkap</h3>
        </div>
        <div class="px-5 py-4">
            <ul id="outValidationMessages" class="text-sm text-slate-700 space-y-1.5 list-disc list-inside"></ul>
        </div>
        <div class="px-5 pb-5">
            <button type="button" onclick="closeOutValidationAlert()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                Perbaiki Isian
            </button>
        </div>
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
        clearOutErrors();

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

    /* ════════════════════════════════════════
       Validasi frontend (nama PT & Google Maps wajib)
    ════════════════════════════════════════ */
    const formEl       = document.getElementById('outStockForm');
    const companyEl    = document.getElementById('outCompanyName');
    const mapEl        = document.getElementById('outMapUrl');
    const companyErrEl = document.getElementById('outCompanyError');
    const mapErrEl     = document.getElementById('outMapError');
    const mapHintEl    = document.getElementById('outMapHint');
    const alertEl      = document.getElementById('outValidationAlert');
    const alertListEl  = document.getElementById('outValidationMessages');

    function setFieldError(input, errorEl, message) {
        if (message) {
            input.classList.add('border-red-400');
            input.classList.remove('border-slate-300');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
        } else {
            input.classList.remove('border-red-400');
            input.classList.add('border-slate-300');
            if (errorEl) { errorEl.classList.add('hidden'); }
        }
    }

    function clearOutErrors() {
        setFieldError(companyEl, companyErrEl, null);
        setFieldError(mapEl, mapErrEl, null);
        mapHintEl.classList.remove('hidden');
        qtyEl.parentElement.style.borderColor = '#cbd5e1';
    }

    function isValidHttpUrl(value) {
        try {
            const u = new URL(value);
            return u.protocol === 'http:' || u.protocol === 'https:';
        } catch (_) {
            return false;
        }
    }

    function validateOutForm() {
        const errors = [];

        // 1. Nama perusahaan penerima
        if (!companyEl.value.trim()) {
            errors.push('Nama Perusahaan Penerima wajib diisi.');
            setFieldError(companyEl, companyErrEl, 'Nama perusahaan penerima wajib diisi.');
        } else {
            setFieldError(companyEl, companyErrEl, null);
        }

        // 2. Link lokasi Google Maps
        const mapVal = mapEl.value.trim();
        if (!mapVal) {
            errors.push('Link Lokasi (Google Maps) wajib diisi.');
            setFieldError(mapEl, mapErrEl, 'Link lokasi (Google Maps) wajib diisi.');
            mapHintEl.classList.add('hidden');
        } else if (!isValidHttpUrl(mapVal)) {
            errors.push('Link Lokasi harus berupa URL yang valid (diawali http:// atau https://).');
            setFieldError(mapEl, mapErrEl, 'Link lokasi tidak valid. Gunakan link yang diawali http:// atau https://.');
            mapHintEl.classList.add('hidden');
        } else {
            setFieldError(mapEl, mapErrEl, null);
            mapHintEl.classList.remove('hidden');
        }

        // 3. Jumlah dikeluarkan
        const qty = parseInt(qtyEl.value, 10);
        const qtyWrap = qtyEl.parentElement;
        if (!qty || qty < 1 || qty > maxQty) {
            errors.push('Jumlah Dikeluarkan harus antara 1 dan ' + maxQty + '.');
            qtyWrap.style.borderColor = '#f87171';
        } else {
            qtyWrap.style.borderColor = '#cbd5e1';
        }

        if (errors.length > 0) {
            alertListEl.innerHTML = errors.map(function (msg) { return '<li>' + msg + '</li>'; }).join('');
            alertEl.style.display = 'flex';
            return false;
        }
        return true;
    }

    window.closeOutValidationAlert = function () {
        alertEl.style.display = 'none';
        // Fokus ke field pertama yang bermasalah
        if (companyEl.classList.contains('border-red-400')) {
            companyEl.focus();
        } else if (mapEl.classList.contains('border-red-400')) {
            mapEl.focus();
        } else {
            qtyEl.focus();
        }
    };

    formEl.addEventListener('submit', function (e) {
        if (!validateOutForm()) { e.preventDefault(); }
    });

    // Tutup alert jika klik backdrop
    alertEl.addEventListener('click', function (e) {
        if (e.target === alertEl) { closeOutValidationAlert(); }
    });

    // Hilangkan state error saat user mulai mengisi
    companyEl.addEventListener('input', function () {
        if (companyEl.value.trim()) { setFieldError(companyEl, companyErrEl, null); }
    });
    mapEl.addEventListener('input', function () {
        if (mapEl.value.trim()) {
            setFieldError(mapEl, mapErrEl, null);
            mapHintEl.classList.remove('hidden');
        }
    });
    qtyEl.addEventListener('input', function () {
        qtyEl.parentElement.style.borderColor = '#cbd5e1';
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        if (alertEl.style.display !== 'none') {
            closeOutValidationAlert();
        } else if (modal.style.display !== 'none') {
            closeOutModal();
        }
    });
})();
</script>