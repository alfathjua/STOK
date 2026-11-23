@extends('dash.layouts.app', ['title' => 'Stok Gudang'])

@section('content')

<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

    {{-- Header + date picker --}}
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

@endsection
