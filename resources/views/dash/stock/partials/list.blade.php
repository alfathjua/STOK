@php use Carbon\Carbon; @endphp

@if ($stocks->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada stok</p>
        <p class="text-xs text-slate-400 mt-1">
            Tambahkan stok melalui
            <a href="{{ route('stock-in.create') }}" class="text-blue-600 hover:underline">Input Stok Masuk</a>.
        </p>
    </div>
@else

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-3 mb-4 text-xs text-slate-500">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded border-2 border-green-400 bg-green-50 inline-block"></span>
            Sisa dari hari sebelumnya
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded border-2 border-red-400 bg-red-50 inline-block"></span>
            Habis dari hari sebelumnya
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded border-2 border-slate-200 bg-white inline-block"></span>
            Stok hari ini
        </span>
    </div>

    <div id="stockCardList" class="space-y-3">
        @foreach ($stocks as $index => $stock)
            @php
                $detail       = $stock->detailStock;
                $isToday      = $stock->date->toDateString() === $today;
                $hasRemaining = $stock->qty_remaining > 0;

                if ($isToday) {
                    $borderClass = 'border-slate-200';
                    $badgeClass  = 'bg-blue-50 text-blue-700';
                    $badgeLabel  = 'Hari ini';
                } elseif ($hasRemaining) {
                    $borderClass = 'border-green-400';
                    $badgeClass  = 'bg-green-50 text-green-700';
                    $badgeLabel  = 'Sisa dari ' . $stock->date->translatedFormat('d M Y');
                } else {
                    $borderClass = 'border-red-400';
                    $badgeClass  = 'bg-red-50 text-red-700';
                    $badgeLabel  = 'Habis · ' . $stock->date->translatedFormat('d M Y');
                }

                $totalValue = $detail->price * $stock->qty_remaining;
            @endphp

            <div class="border-2 {{ $borderClass }} rounded-xl p-4 bg-white stock-card"
                 data-search="{{ strtolower($detail->item_code . ' ' . $detail->item_name) }}"
                 data-index="{{ $index }}">
                <div class="flex items-start justify-between gap-3">

                    {{-- Info barang --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            {{-- Kode barang + tombol copy inline --}}
                            <button type="button"
                                    onclick="copyCode(this, '{{ $detail->item_code }}')"
                                    title="Salin kode barang"
                                    class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold bg-slate-100 hover:bg-blue-100 text-slate-600 hover:text-blue-700 px-2.5 py-1.5 rounded-lg transition active:scale-95 touch-manipulation flex-shrink-0 sm:text-[11px] sm:px-2 sm:py-1">
                                {{ $detail->item_code }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 copy-icon sm:w-3 sm:h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                                <span class="sm:hidden text-[10px] font-normal ml-0.5">Salin</span>
                            </button>
                            </button>
                            <h3 class="text-sm font-semibold text-slate-800 truncate">
                                {{ $detail->item_name }}
                            </h3>
                            <span class="inline-flex items-center text-[10px] font-medium px-2 py-0.5 rounded-full {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        {{-- Asal stok --}}
                        @if ($detail->stockIn?->stock_origin)
                            <p class="flex items-center gap-1 text-xs text-slate-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                                <span>{{ $detail->stockIn->stock_origin }}</span>
                            </p>
                        @endif

                        @if ($detail->description)
                            <p class="text-xs text-slate-400 mb-2 line-clamp-1">{{ $detail->description }}</p>
                        @endif

                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span>
                                <span class="text-slate-400">Harga satuan:</span>
                                <span class="font-medium text-slate-700">Rp {{ number_format($detail->price, 0, ',', '.') }}</span>
                            </span>
                            <span>
                                <span class="text-slate-400">Qty masuk:</span>
                                <span class="font-medium text-slate-700">{{ number_format($stock->qty_in, 0, ',', '.') }}</span>
                            </span>
                            <span>
                                <span class="text-slate-400">Nilai sisa:</span>
                                <span class="font-medium text-slate-700">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Kanan: Sisa stok --}}
                    <div class="text-right flex-shrink-0">
                        <p class="text-[10px] text-slate-400 leading-none mb-0.5">Sisa Stok</p>
                        <p class="text-2xl font-bold leading-none {{ $stock->qty_remaining === 0 ? 'text-red-500' : 'text-slate-800' }}">
                            {{ number_format($stock->qty_remaining, 0, ',', '.') }}
                        </p>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    {{-- Summary footer --}}
    @php
        $totalItems   = $stocks->count();
        $totalHabis   = $stocks->where('qty_remaining', 0)->count();
        $totalAdaSisa = $stocks->where('qty_remaining', '>', 0)->count();
    @endphp
    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
        <span>Total item: <strong class="text-slate-700">{{ $totalItems }}</strong></span>
        <span class="text-green-600">Ada sisa: <strong>{{ $totalAdaSisa }}</strong></span>
        <span class="text-red-500">Habis: <strong>{{ $totalHabis }}</strong></span>
        @if ($totalItems > 20)
            <button type="button" id="stockShowAllBtn"
                    onclick="stockShowAll()"
                    class="ml-auto inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                </svg>
                Lihat Semua ({{ $totalItems }} item)
            </button>
        @endif
    </div>

@endif
