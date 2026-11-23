{{-- ══════════════════════════════════════
     MODAL EDIT QTY
     Dirender di luar container rounded agar position:fixed
     tidak terjebak dalam stacking context parent.
══════════════════════════════════════ --}}
<div id="editStockModal"
     class="fixed inset-0 z-50 bg-black/60 p-4"
     style="display:none; align-items:center; justify-content:center;"
     onclick="closeEditModalOnBackdrop(event)">

    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl"
         id="editStockModalBox"
         style="margin:auto"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div class="min-w-0 pr-3">
                <h3 class="text-sm font-semibold text-slate-800">Edit Sisa Stok</h3>
                <p id="editModalItemName" class="text-xs text-slate-400 mt-0.5 truncate"></p>
            </div>
           <button type="button"
                id="btnPlus"
                onclick="adjustQty(1)"
                style="width:48px; flex-shrink:0; background:#f8fafc; border:none; border-left:1px solid #cbd5e1;
                    cursor:pointer; font-size:1.25rem; font-weight:700; color:#475569; line-height:1;
                    display:flex; align-items:center; justify-content:center;">
            +
        </button>
        </div>

        {{-- Form --}}
        <form id="editStockForm" method="POST" class="p-5">
            @csrf
            @method('PUT')
            <input type="hidden" id="editDateInput" name="date" value="{{ $selectedDate }}">

            <label for="editQtyInput" class="block text-sm font-medium text-slate-700 mb-2">
                Jumlah Sisa Stok <span class="text-red-500">*</span>
            </label>

            <style>
                #editQtyInput::-webkit-outer-spin-button,
                #editQtyInput::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                }
            </style>

           
                        {{-- Stepper (hanya kurangi) --}}
            <div style="display:flex; align-items:stretch; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
                <button type="button"
                        onclick="adjustQty(-1)"
                        style="width:48px; flex-shrink:0; background:#f8fafc; border:none; border-right:1px solid #cbd5e1;
                            cursor:pointer; font-size:1.25rem; font-weight:700; color:#475569; line-height:1;
                            display:flex; align-items:center; justify-content:center;">
                    −
                </button>

                <input type="number"
                    id="editQtyInput"
                    name="qty_remaining"
                    min="0"
                    style="flex:1 1 0%; min-width:0; width:100%; box-sizing:border-box;
                            text-align:center; font-size:1.5rem; font-weight:700; padding:0.75rem 0.5rem;
                            border:none; outline:none; background:#fff; color:#1e293b;
                            -moz-appearance:textfield; appearance:textfield;">
            </div>

            <p class="text-xs text-slate-400 mt-1.5 mb-5">Masukkan angka sisa stok aktual di gudang.</p>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                    Simpan
                </button>
                <button type="button"
                        onclick="closeEditModal()"
                        class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium py-2.5 rounded-lg transition text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal   = document.getElementById('editStockModal');
    const form    = document.getElementById('editStockForm');
    const nameEl  = document.getElementById('editModalItemName');
    const qtyEl   = document.getElementById('editQtyInput');
    const plusBtn = document.getElementById('btnPlus');

    let maxQty = 0; // batas atas = stok saat modal dibuka

    function clamp(val) {
        val = parseInt(val, 10);
        if (isNaN(val)) val = 0;
        return Math.min(maxQty, Math.max(0, val));
    }

    function syncPlusButton() {
        plusBtn.disabled = (parseInt(qtyEl.value, 10) || 0) >= maxQty;
    }

    window.openEditModal = function (stockId, itemName, currentQty) {
        maxQty = parseInt(currentQty, 10) || 0;

        form.action = '/stock/' + stockId + '?date=' + document.getElementById('editDateInput').value;
        nameEl.textContent = itemName;
        qtyEl.max   = maxQty;
        qtyEl.value = maxQty;
        syncPlusButton();

        modal.style.display = 'flex';
        setTimeout(function () { qtyEl.focus(); qtyEl.select(); }, 50);
    };

    window.closeEditModal = function () {
        modal.style.display = 'none';
    };

    window.closeEditModalOnBackdrop = function (e) {
        if (e.target === modal) closeEditModal();
    };

    window.adjustQty = function (delta) {
        qtyEl.value = clamp((parseInt(qtyEl.value, 10) || 0) + delta);
        syncPlusButton();
    };

    // Kalau user mengetik manual angka yang lebih besar, paksa turun ke batas
    qtyEl.addEventListener('input', function () {
        if (qtyEl.value !== '' && parseInt(qtyEl.value, 10) > maxQty) {
            qtyEl.value = maxQty;
        }
        syncPlusButton();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') {
            closeEditModal();
        }
    });
})();
</script>