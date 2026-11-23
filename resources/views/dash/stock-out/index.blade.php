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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Riwayat Keluar
        </a>
    </div>

    {{-- Flash success --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @include('dash.stock-out.partials.list', ['stocks' => $stocks])
</div>

{{-- Modal keluar — di luar container agar fixed tidak terjebak stacking context --}}
@include('dash.stock-out.partials.modal-out')

@endsection
