@extends('dash.layouts.app', ['title' => 'Form Stok Masuk'])

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-xl border border-slate-200 p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-1">Form Stok Masuk</h2>
    <p class="text-sm text-slate-500 mb-6">Isi detail barang yang masuk.</p>

    <form action="#" method="POST" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Asal Stok</label>
            <input type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition" placeholder="Contoh: Supplier A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Bukti / Faktur</label>
            <input type="file" class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 file:font-medium hover:file:bg-blue-100 transition">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg transition">Simpan</button>
            <a href="{{ route('stock-in.index') }}" class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-medium px-5 py-2.5 rounded-lg transition">Batal</a>
        </div>
    </form>
</div>

@endsection
