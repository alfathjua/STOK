@extends('dash.layouts.app', ['title' => 'Beranda'])

@php
    $user = auth()->user();
    $isDirector = $user->role === 'director';
@endphp

@section('content')

@if ($isDirector)
    {{-- ============ Director: Summary + Charts ============ --}}

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Stok Masuk --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-500">Stok Masuk</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">128</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 4.5-4.5m-4.5 4.5L7.5 12" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400">Bulan ini &middot; <span class="text-green-600 font-medium">+12%</span></p>
        </div>

        {{-- Stok Gudang --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-500">Stok Gudang</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">856</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400">Total item &middot; <span class="text-slate-600 font-medium">tersedia</span></p>
        </div>

        {{-- Stok Keluar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-500">Stok Keluar</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">72</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400">Bulan ini &middot; <span class="text-red-500 font-medium">-8%</span></p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Stock In / Out chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-1">Stok Masuk &amp; Keluar</h3>
            <p class="text-xs text-slate-400 mb-5">6 bulan terakhir</p>
            <div class="flex items-end justify-between gap-3 h-40">
                @php
                    // [month, in, out]
                    $chartData = [
                        ['Jun', 45, 30],
                        ['Jul', 60, 40],
                        ['Agu', 55, 35],
                        ['Sep', 70, 50],
                        ['Okt', 80, 45],
                        ['Nov', 95, 60],
                    ];
                    $maxIn  = max(array_column($chartData, 1));
                    $maxOut = max(array_column($chartData, 2));
                    $maxVal = max($maxIn, $maxOut);
                @endphp
                @foreach ($chartData as $row)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full flex items-end justify-center gap-1 h-40">
                            <div class="w-3 sm:w-4 bg-blue-600 rounded-t" style="height: {{ round($row[1] / $maxVal * 100) }}%"></div>
                            <div class="w-3 sm:w-4 bg-slate-300 rounded-t" style="height: {{ round($row[2] / $maxVal * 100) }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $row[0] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-blue-600"></span> Masuk
                </span>
                <span class="flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-slate-300"></span> Keluar
                </span>
            </div>
        </div>

        {{-- Warehouse stock chart --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-1">Stok Gudang per Kategori</h3>
            <p class="text-xs text-slate-400 mb-5">Distribusi item</p>
            <div class="space-y-4">
                @php
                    $categories = [
                        ['name' => 'Material',     'value' => 320, 'color' => 'bg-blue-600'],
                        ['name' => 'Peralatan',   'value' => 250, 'color' => 'bg-blue-400'],
                        ['name' => 'Suku Cadang', 'value' => 180, 'color' => 'bg-slate-400'],
                        ['name' => 'Lainnya',     'value' => 106, 'color' => 'bg-slate-300'],
                    ];
                    $maxCat = max(array_column($categories, 'value'));
                @endphp
                @foreach ($categories as $cat)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm text-slate-600">{{ $cat['name'] }}</span>
                            <span class="text-sm font-medium text-slate-800">{{ $cat['value'] }}</span>
                        </div>
                        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $cat['color'] }}" style="width: {{ round($cat['value'] / $maxCat * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@else
    {{-- ============ Foreman: Motivational message ============ --}}
    <div class="flex items-center justify-center" style="min-height: calc(100vh - 8rem)">
        <div class="max-w-lg text-center">
            <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-3">Selamat Datang, {{ $user->name }}!</h2>
            <p class="text-slate-500 leading-relaxed">
                Setiap pekerjaan yang baik dimulai dari kedisiplinan yang konsisten.
                Mari kelola stok gudang dengan teliti hari ini &mdash; sebab ketepatan di gudang
                adalah fondasi dari kelancaran seluruh operasional.
            </p>
            <p class="text-slate-400 text-sm mt-4">Semangat bekerja!</p>
        </div>
    </div>
@endif

@endsection
