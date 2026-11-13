<div class="overflow-x-auto -mx-4 sm:mx-0">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-left text-xs font-medium text-slate-400 border-b border-slate-200">
                <th class="py-3 px-4 sm:px-0 sm:pr-4">#</th>
                <th class="py-3 pr-4">Asal Stok</th>
                <th class="py-3 pr-4">Dicatat Oleh</th>
                <th class="py-3 pr-4">Jml Item</th>
                <th class="py-3 pr-4">Total Nilai</th>
                <th class="py-3 pr-4">Struk</th>
                <th class="py-3 pr-4">Tanggal</th>
                <th class="py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($stockIns as $item)
                <tr class="hover:bg-slate-50 transition">
                    {{-- No --}}
                    <td class="py-3 px-4 sm:px-0 sm:pr-4 text-slate-400">
                        {{ $loop->iteration + ($stockIns->firstItem() - 1) }}
                    </td>

                    {{-- Asal stok --}}
                    <td class="py-3 pr-4 font-medium text-slate-800">
                        {{ $item->stock_origin }}
                    </td>

                    {{-- User --}}
                    <td class="py-3 pr-4 text-slate-500">
                        {{ $item->user->name ?? '-' }}
                    </td>

                    {{-- Jumlah item lines --}}
                    <td class="py-3 pr-4 text-slate-700">
                        {{ $item->details->count() }} item
                    </td>

                    {{-- Total nilai --}}
                    <td class="py-3 pr-4 text-slate-700 whitespace-nowrap">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                    </td>

                    {{-- Struk --}}
                    <td class="py-3 pr-4 text-slate-500">
                        @php $rc = $item->receipts->count(); @endphp
                        {{ $rc > 0 ? $rc.' struk' : '—' }}
                    </td>

                    {{-- Tanggal --}}
                    <td class="py-3 pr-4 text-slate-500 whitespace-nowrap">
                        {{ $item->created_at->format('d M Y') }}
                    </td>

                    {{-- Aksi --}}
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('stock-in.edit', $item) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('stock-in.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Hapus stok masuk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">
                        Belum ada data stok masuk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($stockIns->hasPages())
    <div class="mt-5 pt-4 border-t border-slate-100">
        {{ $stockIns->links() }}
    </div>
@endif
