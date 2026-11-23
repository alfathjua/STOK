@if ($stockOuts->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada riwayat stok keluar</p>
        <p class="text-xs text-slate-400 mt-1">
            Keluarkan stok dari halaman
            <a href="{{ route('stock-out.index') }}" class="text-blue-600 hover:underline">Input Stok Keluar</a>.
        </p>
    </div>
@else
    {{-- Desktop: table --}}
    <div class="hidden sm:block overflow-x-auto -mx-4 sm:mx-0">
        <table class="w-full text-sm min-w-[700px]">
            <thead>
                <tr class="text-left text-xs font-medium text-slate-400 border-b border-slate-200">
                    <th class="py-3 px-4 sm:px-0 sm:pr-4">#</th>
                    <th class="py-3 pr-4">Nama Barang</th>
                    <th class="py-3 pr-4">Perusahaan Penerima</th>
                    <th class="py-3 pr-4">Lokasi</th>
                    <th class="py-3 pr-4">Qty</th>
                    <th class="py-3 pr-4">Harga Satuan</th>
                    <th class="py-3 pr-4">Total Nilai</th>
                    <th class="py-3 pr-4">Dicatat Oleh</th>
                    <th class="py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($stockOuts as $out)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 sm:px-0 sm:pr-4 text-slate-400 text-xs">
                            {{ $loop->iteration + ($stockOuts->firstItem() - 1) }}
                        </td>
                        <td class="py-3 pr-4 font-medium text-slate-800">
                            {{ $out->detailStock->item_name ?? '—' }}
                        </td>
                        <td class="py-3 pr-4 text-slate-700">
                            {{ $out->company_name }}
                        </td>
                        <td class="py-3 pr-4">
                            @if ($out->map_url)
                                <a href="{{ $out->map_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-xs font-medium transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    Lihat Peta
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-slate-700">
                            {{ number_format($out->qty, 0, ',', '.') }}
                        </td>
                        <td class="py-3 pr-4 text-slate-700 whitespace-nowrap">
                            Rp {{ number_format($out->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 pr-4 text-slate-700 whitespace-nowrap">
                            Rp {{ number_format($out->qty * $out->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 pr-4 text-slate-500 text-xs">
                            {{ $out->user->name ?? '—' }}
                        </td>
                        <td class="py-3 text-slate-500 text-xs whitespace-nowrap">
                            {{ $out->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile: card list --}}
    <div class="sm:hidden space-y-3">
        @foreach ($stockOuts as $out)
            <div class="border border-slate-200 rounded-xl p-4 bg-white">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ $out->detailStock->item_name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $out->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="flex-shrink-0 text-sm font-bold text-slate-800">
                        ×{{ number_format($out->qty, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-2">
                    <span>
                        <span class="text-slate-400">Penerima:</span>
                        <span class="font-medium text-slate-700">{{ $out->company_name }}</span>
                    </span>
                    <span>
                        <span class="text-slate-400">Total:</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($out->qty * $out->price, 0, ',', '.') }}</span>
                    </span>
                    <span>
                        <span class="text-slate-400">Oleh:</span>
                        <span class="font-medium text-slate-700">{{ $out->user->name ?? '—' }}</span>
                    </span>
                </div>

                @if ($out->map_url)
                    <a href="{{ $out->map_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-xs font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        Lihat Lokasi di Peta
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($stockOuts->hasPages())
        <div class="mt-5 pt-4 border-t border-slate-100">
            {{ $stockOuts->links() }}
        </div>
    @endif

    {{-- Summary footer --}}
    @php
        $grandTotal = $stockOuts->sum(fn ($o) => $o->qty * $o->price);
    @endphp
    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
        <span>Total transaksi: <strong class="text-slate-700">{{ $stockOuts->total() }}</strong></span>
        <span>Total nilai: <strong class="text-slate-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></span>
    </div>
@endif
