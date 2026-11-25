@extends('dash.layouts.app', ['title' => 'Input Stok Keluar'])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Input Stok Keluar</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Pilih barang yang akan dikeluarkan dari gudang hari ini.
                </p>
            </div>
            <a href="{{ route('stock-out.history') }}"
                class="inline-flex items-center gap-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Keluar
            </a>
        </div>

        @if (session('success'))
            <div
                class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <div class="relative max-w-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </span>
                <input type="text" id="outSearch" placeholder="Cari kode (KB-001) atau nama barang…" autocomplete="off"
                    oninput="filterOutCards(this.value)"
                    class="w-full pl-9 pr-9 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                <button type="button" id="outSearchClear"
                    onclick="document.getElementById('outSearch').value=''; filterOutCards('');"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        @include('dash.stock-out.partials.list', ['stocks' => $stocks])
    </div>
    @include('dash.stock-out.partials.modal-out')

    <script>
        var outShowAllMode = false;
        const OUT_LIMIT = 20;

        function applyOutVisibility(query) {
            const q = (query || '').trim().toLowerCase();
            document.getElementById('outSearchClear').classList.toggle('hidden', q === '');

            document.querySelectorAll('.out-card').forEach(function(card) {
                const matchesSearch = q === '' || (card.dataset.search || '').includes(q);
                const withinLimit = outShowAllMode || parseInt(card.dataset.index) < OUT_LIMIT;
                card.style.display = (matchesSearch && withinLimit) ? '' : 'none';
            });

            var btn = document.getElementById('outShowAllBtn');
            if (btn) {
                btn.style.display = (outShowAllMode || q !== '') ? 'none' : '';
            }
        }

        function filterOutCards(query) {
            applyOutVisibility(query);
        }

        function outShowAll() {
            outShowAllMode = true;
            applyOutVisibility(document.getElementById('outSearch').value);
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyOutVisibility('');
        });
    </script>
@endsection
