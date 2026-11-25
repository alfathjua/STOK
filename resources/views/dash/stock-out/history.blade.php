@extends('dash.layouts.app', ['title' => 'Riwayat Stok Keluar'])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Riwayat Stok Keluar</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Daftar seluruh barang yang telah dikeluarkan dari gudang.
                </p>
            </div>
            <a href="{{ route('stock-out.index') }}"
                class="inline-flex items-center gap-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Kembali
            </a>
        </div>

        <form method="GET" action="{{ route('stock-out.history') }}" class="mb-5">
            <div class="relative max-w-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari kode (KB-001) atau nama barang…" autocomplete="off"
                    class="w-full pl-9 pr-9 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                @if (!empty($search))
                    <a href="{{ route('stock-out.history') }}"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>

        @include('dash.stock-out.partials.history-list', ['stockOuts' => $stockOuts])
    </div>
@endsection
