@extends('dash.layouts.app', ['title' => 'Riwayat Stok Keluar'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Riwayat Stok Keluar</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Daftar seluruh barang yang telah dikeluarkan dari gudang.
            </p>
        </div>
        <a href="{{ route('stock-out.index') }}"
           class="inline-flex items-center gap-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-3 py-2 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            Kembali
        </a>
    </div>

    @include('dash.stock-out.partials.history-list', ['stockOuts' => $stockOuts])
</div>

@endsection
