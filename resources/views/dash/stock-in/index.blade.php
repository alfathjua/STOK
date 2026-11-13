@extends('dash.layouts.app', ['title' => 'Input Stok Masuk'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-1">Input Stok Masuk</h2>
    <p class="text-sm text-slate-500 mb-6">Catat barang yang masuk ke gudang.</p>

    {{-- Action buttons --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <button type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Stok Masuk
        </button>
    </div>

    {{-- Placeholder list --}}
    @include('dash.stock-in.partials.list')
</div>

@endsection
