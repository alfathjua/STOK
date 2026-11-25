@if ($logs->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600">Belum ada aktivitas tercatat</p>
        <p class="text-xs text-slate-400 mt-1">Aktivitas akan muncul setelah foreman melakukan aksi di sistem.</p>
    </div>
@else
    <div class="space-y-2">
        @foreach ($logs as $log)
            @php
                $color = $log->action_color;
                if ($log->action === 'create') {
                    $isStockOut = \Illuminate\Support\Str::contains(strtolower($log->feature), 'keluar');
                    $color = $isStockOut ? 'red' : 'green';
                }

                $borderClass = match ($color) {
                    'green' => 'border-green-400',
                    'blue' => 'border-blue-400',
                    'red' => 'border-red-400',
                    default => 'border-slate-200',
                };

                $badgeBg = match ($color) {
                    'green' => 'bg-green-50 text-green-700',
                    'blue' => 'bg-blue-50 text-blue-700',
                    'red' => 'bg-red-50 text-red-700',
                    default => 'bg-slate-100 text-slate-600',
                };

                $dotColor = match ($color) {
                    'green' => 'bg-green-400',
                    'blue' => 'bg-blue-400',
                    'red' => 'bg-red-400',
                    default => 'bg-slate-300',
                };
                $decoded = json_decode($log->description, true);
                $isJson = is_array($decoded);
                $summary = $isJson ? $decoded['summary'] ?? $log->description : $log->description;
                $items = $isJson && isset($decoded['items']) ? $decoded['items'] : [];
                $origin = $isJson && isset($decoded['origin']) ? $decoded['origin'] : null;
                $company = $isJson && isset($decoded['company']) ? $decoded['company'] : null;
                $singleItem = $isJson && isset($decoded['item_name']) ? $decoded : null;
            @endphp

            <div
                class="border-l-4 {{ $borderClass }} bg-white rounded-r-xl px-4 py-3 flex items-start gap-3 shadow-sm">
                <div class="mt-1 flex-shrink-0">
                    <span class="inline-block w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                        <span
                            class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeBg }}">
                            {{ $log->action_label }}
                        </span>
                        <span class="text-xs font-medium text-slate-700">{{ $log->feature }}</span>
                        @if ($origin)
                            <span
                                class="inline-flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                                {{ $origin }}
                            </span>
                        @elseif ($company)
                            <span
                                class="inline-flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                {{ $company }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600">{{ $summary }}</p>
                    @if (count($items) > 0)
                        <div class="mt-2 space-y-1">
                            @foreach ($items as $item)
                                <div
                                    class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 rounded-lg px-3 py-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-3.5 h-3.5 text-slate-400 flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                    <span class="font-medium text-slate-700 flex-1">{{ $item['name'] }}</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($item['qty']) }}
                                        pcs</span>
                                    @if (!empty($item['price']) && $item['price'] > 0)
                                        <span class="text-slate-400">·</span>
                                        <span class="text-slate-500">Rp
                                            {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if ($singleItem)
                        <div class="mt-2">
                            <div
                                class="flex items-center gap-2 text-xs text-slate-600 bg-red-50 rounded-lg px-3 py-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-3.5 h-3.5 text-red-400 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                <span class="font-medium text-slate-700 flex-1">{{ $singleItem['item_name'] }}</span>
                                <span class="font-semibold text-red-700">-{{ number_format($singleItem['qty']) }}
                                    pcs</span>
                                @if (!empty($singleItem['price']) && $singleItem['price'] > 0)
                                    <span class="text-slate-400">·</span>
                                    <span class="text-slate-500">Rp
                                        {{ number_format($singleItem['price'], 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-2 text-xs text-slate-400">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            {{ $log->user->name ?? 'Pengguna dihapus' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    @if ($logs->hasPages())
        <div class="mt-5 pt-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    @endif

    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
        <span>Total aktivitas: <strong class="text-slate-700">{{ $logs->total() }}</strong></span>
    </div>

@endif
