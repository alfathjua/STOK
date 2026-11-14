@php
    $features = [
        ['route' => 'stock-in.index',  'label' => 'Stok Masuk',  'icon' => 'in'],
        ['route' => 'stock.index',     'label' => 'Stok Gudang', 'icon' => 'box'],
        ['route' => 'stock-out.index', 'label' => 'Stok Keluar', 'icon' => 'out'],
    ];

    $gridIcons = [
        'in'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 4.5-4.5m-4.5 4.5L7.5 12" />',
        'box' => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />',
        'out' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5" />',
    ];
@endphp

<div class="grid grid-cols-3 gap-2 sm:gap-3">
    @foreach ($features as $feature)
        <a href="{{ route($feature['route']) }}"
           class="group flex flex-col items-center gap-2 bg-white rounded-xl border border-slate-200 p-3 sm:p-4 hover:border-blue-400 hover:shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600 group-hover:text-blue-700 transition">
                {!! $gridIcons[$feature['icon']] !!}
            </svg>
            <span class="text-xs sm:text-sm font-medium text-slate-700 text-center leading-tight">{{ $feature['label'] }}</span>
        </a>
    @endforeach
</div>
