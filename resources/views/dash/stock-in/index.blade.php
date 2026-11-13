@extends('dash.layouts.app', ['title' => 'Input Stok Masuk'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Daftar seluruh pencatatan stok masuk</h2>
        </div>
        <a href="{{ route('stock-in.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span class="hidden sm:inline">Stok Masuk</span>
            <span class="sm:hidden">+</span>
        </a>
    </div>

    {{-- Success flash --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @include('dash.stock-in.partials.list', ['stockIns' => $stockIns])
</div>

@endsection
