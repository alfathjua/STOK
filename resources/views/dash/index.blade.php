@extends('dash.layouts.app', ['title' => 'Beranda'])

@php
    $user = auth()->user();
    $isDirector = $user->role === 'director';
@endphp

@section('content')

@if ($isDirector)

    {{-- ════════════════════════════════════════
         MOBILE LAYOUT (< lg)
    ════════════════════════════════════════ --}}
    <div class="lg:hidden space-y-4">

        {{-- 1. Summary slider --}}
        <div class="relative overflow-hidden" id="summarySlider">
            <div id="summaryTrack" class="flex transition-transform duration-500 ease-in-out">

                {{-- Slide 1: Stok Masuk --}}
                <div class="min-w-full px-0.5">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Stok Masuk</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">128</p>
                            <p class="text-[11px] text-slate-400 mt-1.5">Bulan ini &middot; <span class="text-green-600 font-medium">+12%</span></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 4.5-4.5m-4.5 4.5L7.5 12" />
                        </svg>
                    </div>
                </div>

                {{-- Slide 2: Stok Gudang --}}
                <div class="min-w-full px-0.5">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Stok Gudang</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">856</p>
                            <p class="text-[11px] text-slate-400 mt-1.5">Total item &middot; <span class="text-slate-600 font-medium">tersedia</span></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>

                {{-- Slide 3: Stok Keluar --}}
                <div class="min-w-full px-0.5">
                    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Stok Keluar</p>
                            <p class="text-2xl font-bold text-slate-800 mt-1">72</p>
                            <p class="text-[11px] text-slate-400 mt-1.5">Bulan ini &middot; <span class="text-red-500 font-medium">-8%</span></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5" />
                        </svg>
                    </div>
                </div>

            </div>

            {{-- Dot indicators --}}
            <div class="flex justify-center gap-1.5 mt-2">
                <span class="slider-dot w-1.5 h-1.5 rounded-full bg-blue-600 transition-all duration-300"></span>
                <span class="slider-dot w-1.5 h-1.5 rounded-full bg-slate-300 transition-all duration-300"></span>
                <span class="slider-dot w-1.5 h-1.5 rounded-full bg-slate-300 transition-all duration-300"></span>
            </div>
        </div>

        {{-- 2. Grid menu fitur --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-800 mb-2 px-1">Menu Fitur</h2>
            @include('dash.layouts.grid')
        </div>

        {{-- 3. Grafik Stok Masuk & Keluar (hanya 1, tanpa kategori) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <h3 class="text-sm font-semibold text-slate-800 mb-1">Stok Masuk &amp; Keluar</h3>
            <p class="text-[11px] text-slate-400 mb-4">6 bulan terakhir</p>
            <div class="flex items-end justify-between gap-2 h-28">
                @php
                    $chartData = [
                        ['Jun', 45, 30],
                        ['Jul', 60, 40],
                        ['Agu', 55, 35],
                        ['Sep', 70, 50],
                        ['Okt', 80, 45],
                        ['Nov', 95, 60],
                    ];
                    $maxVal = max(max(array_column($chartData, 1)), max(array_column($chartData, 2)));
                @endphp
                @foreach ($chartData as $row)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full flex items-end justify-center gap-0.5 h-24">
                            <div class="w-3 bg-blue-600 rounded-t" style="height: {{ round($row[1] / $maxVal * 100) }}%"></div>
                            <div class="w-3 bg-slate-300 rounded-t"  style="height: {{ round($row[2] / $maxVal * 100) }}%"></div>
                        </div>
                        <span class="text-[9px] text-slate-400">{{ $row[0] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-3 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded bg-blue-600"></span> Masuk</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded bg-slate-300"></span> Keluar</span>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════
         DESKTOP LAYOUT (lg+)
    ════════════════════════════════════════ --}}
    <div class="hidden lg:block space-y-6">

        {{-- Summary cards --}}
        <div class="grid grid-cols-3 gap-4">
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

        {{-- Chart: Stok Masuk & Keluar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-1">Stok Masuk &amp; Keluar</h3>
            <p class="text-xs text-slate-400 mb-5">6 bulan terakhir</p>
            <div class="flex items-end justify-between gap-3 h-40">
                @foreach ($chartData as $row)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full flex items-end justify-center gap-1 h-40">
                            <div class="w-4 bg-blue-600 rounded-t" style="height: {{ round($row[1] / $maxVal * 100) }}%"></div>
                            <div class="w-4 bg-slate-300 rounded-t"  style="height: {{ round($row[2] / $maxVal * 100) }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $row[0] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 rounded bg-blue-600"></span> Masuk</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 rounded bg-slate-300"></span> Keluar</span>
            </div>
        </div>

    </div>

    {{-- Slider JS (mobile only) --}}
    <script>
    (function () {
        const track  = document.getElementById('summaryTrack');
        const dots   = document.querySelectorAll('.slider-dot');
        if (!track) return;

        const total      = 3;
        let   current    = 0;
        let   paused     = false;
        let   interval   = null;

        function goTo(idx) {
            current = (idx + total) % total;
            track.style.transform = `translateX(-${current * 100}%)`;
            dots.forEach((d, i) => {
                d.classList.toggle('bg-blue-600', i === current);
                d.classList.toggle('w-3',         i === current);
                d.classList.toggle('bg-slate-300', i !== current);
                d.classList.toggle('w-1.5',        i !== current);
            });
        }

        function next() { goTo(current + 1); }

        function startAuto() {
            clearInterval(interval);
            interval = setInterval(() => { if (!paused) next(); }, 8000);
        }

        // Touch: pause on touchstart, resume after 12s of inactivity
        let resumeTimer = null;
        track.closest('#summarySlider').addEventListener('touchstart', () => {
            paused = true;
            clearTimeout(resumeTimer);
            resumeTimer = setTimeout(() => { paused = false; }, 12000);
        }, { passive: true });

        // Swipe detection
        let touchStartX = 0;
        track.closest('#summarySlider').addEventListener('touchstart', e => {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });
        track.closest('#summarySlider').addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
        }, { passive: true });

        startAuto();
        goTo(0);
    })();
    </script>

@else
    {{-- ============ Foreman: Motivational message ============ --}}

    {{-- Mobile: grid menu dulu, lalu pesan motivasi --}}
    <div class="lg:hidden mb-4">
        <h2 class="text-sm font-semibold text-slate-800 mb-2 px-1">Menu Fitur</h2>
        @include('dash.layouts.grid')
    </div>

    <div class="flex items-center justify-center" style="min-height: calc(100vh - 20rem)">
        <div class="max-w-sm sm:max-w-lg text-center px-2">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2 sm:mb-3">Selamat Datang, {{ $user->name }}!</h2>
            <p class="text-sm sm:text-base text-slate-500 leading-relaxed">
                Setiap pekerjaan yang baik dimulai dari kedisiplinan yang konsisten.
                Mari kelola stok gudang dengan teliti hari ini &mdash; sebab ketepatan di gudang
                adalah fondasi dari kelancaran seluruh operasional.
            </p>
            <p class="text-slate-400 text-xs sm:text-sm mt-3 sm:mt-4">Semangat bekerja!</p>
        </div>
    </div>
@endif

@endsection
