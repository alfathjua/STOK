@if ($logs->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
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
                $color = $log->action_color; // green | blue | red

                $borderClass = match ($color) {
                    'green' => 'border-green-400',
                    'blue'  => 'border-blue-400',
                    'red'   => 'border-red-400',
                    default => 'border-slate-200',
                };

                $badgeBg = match ($color) {
                    'green' => 'bg-green-50 text-green-700',
                    'blue'  => 'bg-blue-50 text-blue-700',
                    'red'   => 'bg-red-50 text-red-700',
                    default => 'bg-slate-100 text-slate-600',
                };

                $dotColor = match ($color) {
                    'green' => 'bg-green-400',
                    'blue'  => 'bg-blue-400',
                    'red'   => 'bg-red-400',
                    default => 'bg-slate-300',
                };
            @endphp

            <div class="border-l-4 {{ $borderClass }} bg-white rounded-r-xl px-4 py-3 flex items-start gap-3 shadow-sm">

                {{-- Dot warna aksi --}}
                <div class="mt-1 flex-shrink-0">
                    <span class="inline-block w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                </div>

                {{-- Konten log --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                        {{-- Badge aksi --}}
                        <span class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeBg }}">
                            {{ $log->action_label }}
                        </span>
                        {{-- Fitur --}}
                        <span class="text-xs font-medium text-slate-700">{{ $log->feature }}</span>
                    </div>

                    <p class="text-sm text-slate-600">{{ $log->description }}</p>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1 text-xs text-slate-400">
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            {{ $log->user->name ?? 'Pengguna dihapus' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($logs->hasPages())
        <div class="mt-5 pt-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    @endif

    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-xs text-slate-500">
        <span>Total aktivitas: <strong class="text-slate-700">{{ $logs->total() }}</strong></span>
    </div>

@endif
