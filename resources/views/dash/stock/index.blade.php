@extends('dash.layouts.app', ['title' => 'Stok Gudang'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

    {{-- Date picker + Search bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Stok Gudang</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Informasi stok barang di gudang. Untuk mengubah data barang, gunakan
                <a href="{{ route('stock-in.index') }}" class="text-blue-600 hover:underline">Input Stok Masuk</a>.
            </p>
        </div>

        {{-- Date picker — tidak bisa pilih masa depan --}}
        <form method="GET" action="{{ route('stock.index') }}" id="dateFilterForm">
            <div class="flex items-center gap-2">
                <label for="dateInput" class="text-xs text-slate-500 whitespace-nowrap">Tanggal:</label>
                <input
                    type="date"
                    id="dateInput"
                    name="date"
                    value="{{ $selectedDate }}"
                    max="{{ $today }}"
                    class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition"
                    onchange="document.getElementById('dateFilterForm').submit()"
                >
            </div>
        </form>
    </div>

    {{-- Search bar client-side --}}
    <div class="mb-4">
        <div class="relative max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <input type="text" id="stockSearch"
                   placeholder="Cari kode (KB-001) atau nama barang…"
                   autocomplete="off"
                   oninput="filterStockCards(this.value)"
                   class="w-full pl-9 pr-9 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
            <button type="button" id="stockSearchClear"
                    onclick="document.getElementById('stockSearch').value=''; filterStockCards('');"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 hidden">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Badge tanggal aktif --}}
    <div class="flex items-center gap-2 mb-4">
        <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
            {{ $selectedDate === $today ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            {{ $selectedDate === $today ? 'Hari ini' : 'Tanggal lalu' }}:
            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
        </span>
        @if ($selectedDate !== $today)
            <a href="{{ route('stock.index') }}" class="text-xs text-slate-400 hover:text-slate-600 transition underline">
                Kembali ke hari ini
            </a>
        @endif
    </div>

    @include('dash.stock.partials.list', [
        'stocks'       => $stocks,
        'selectedDate' => $selectedDate,
        'today'        => $today,
    ])
</div>

<script>
var stockShowAllMode = false;
const STOCK_LIMIT = 20;

function applyStockVisibility(query) {
    const q = (query || '').trim().toLowerCase();
    document.getElementById('stockSearchClear').classList.toggle('hidden', q === '');

    var shown = 0;
    document.querySelectorAll('.stock-card').forEach(function (card) {
        const matchesSearch = q === '' || (card.dataset.search || '').includes(q);
        const withinLimit   = stockShowAllMode || parseInt(card.dataset.index) < STOCK_LIMIT;
        card.style.display  = (matchesSearch && withinLimit) ? '' : 'none';
        if (matchesSearch) { shown++; }
    });

    var btn = document.getElementById('stockShowAllBtn');
    if (btn) { btn.style.display = (stockShowAllMode || q !== '') ? 'none' : ''; }
}

function filterStockCards(query) {
    applyStockVisibility(query);
}

function stockShowAll() {
    stockShowAllMode = true;
    applyStockVisibility(document.getElementById('stockSearch').value);
}

function copyCode(btn, code) {
    navigator.clipboard.writeText(code).then(function () {
        var icon = btn.querySelector('.copy-icon');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />';
        btn.classList.add('bg-green-100', 'text-green-700');
        btn.classList.remove('bg-slate-100', 'hover:bg-blue-100', 'text-slate-500', 'hover:text-blue-700');
        setTimeout(function () {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />';
            btn.classList.remove('bg-green-100', 'text-green-700');
            btn.classList.add('bg-slate-100', 'text-slate-500');
        }, 1500);
    });
}

// Init — sembunyikan item > 20 saat halaman load
document.addEventListener('DOMContentLoaded', function () { applyStockVisibility(''); });
</script>

@endsection
