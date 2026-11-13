@extends('dash.layouts.app', ['title' => 'Stok Gudang'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-1">Stok Gudang</h2>
    <p class="text-sm text-slate-500 mb-6">Daftar barang tersimpan di gudang.</p>

    @include('dash.stock.partials.list')
</div>

@endsection
