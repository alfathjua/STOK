@if ($stocks->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Tidak ada stok tersedia</p>
        <p class="text-xs text-slate-400 mt-1">
            Tambah stok melalui
            <a href="{{ route('stock-in.create') }}" class="text-blue-600 hover:underline">Input Stok Masuk</a>.
        </p>
    </div>
@else
    <div id="outCardList" class="space-y-3">
        @foreach ($stocks as $index => $stock)
            @php
                $detail = $stock->detailStock;
                $totalValue = $detail->price * $stock->qty_remaining;
            @endphp

            <div class="border border-slate-200 rounded-xl p-4 bg-white hover:shadow-sm transition out-card"
                data-search="{{ strtolower($detail->item_code . ' ' . $detail->item_name) }}"
                data-index="{{ $index }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span
                                class="font-mono text-xs font-semibold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg flex-shrink-0">
                                {{ $detail->item_code }}
                            </span>
                            <h3 class="text-sm font-semibold text-slate-800 truncate">
                                {{ $detail->item_name }}
                            </h3>
                        </div>

                        @if ($detail->stockIn?->stock_origin)
                            <p class="flex items-center gap-1 text-xs text-slate-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
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
                                <span class="font-medium text-slate-700">Rp
                                    {{ number_format($detail->price, 0, ',', '.') }}</span>
                            </span>
                            <span>
                                <span class="text-slate-400">Nilai sisa:</span>
                                <span class="font-medium text-slate-700">Rp
                                    {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 leading-none mb-0.5">Sisa Stok</p>
                            <p class="text-2xl font-bold leading-none text-slate-800">
                                {{ number_format($stock->qty_remaining, 0, ',', '.') }}
                            </p>
                        </div>
                        <button type="button"
                            onclick="openOutModal({{ $stock->id }}, {{ json_encode($detail->item_name) }}, {{ $stock->qty_remaining }}, {{ (float) $detail->price }})"
                            class="inline-flex items-center gap-1 text-xs font-medium text-white bg-red-500 hover:bg-red-600 px-2.5 py-1.5 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5" />
                            </svg>
                            Keluarkan
                        </button>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-4">
        <span>Total barang tersedia: <strong class="text-slate-700">{{ $stocks->count() }} item</strong></span>
        @if ($stocks->count() > 20)
            <button type="button" id="outShowAllBtn" onclick="outShowAll()"
                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                </svg>
                Lihat Semua ({{ $stocks->count() }} item)
            </button>
        @endif
    </div>
@endif
