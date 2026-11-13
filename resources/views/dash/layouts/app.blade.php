<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') &mdash; JadStock</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-slate-100">

@php
    $user = auth()->user();

    // Sidebar menu items (desktop) — all features + company profile for director
    $menuItems = [
        ['route' => 'dashboard',      'label' => 'Beranda',           'icon' => 'home'],
        ['route' => 'stock-in.index',  'label' => 'Input Stok Masuk', 'icon' => 'in'],
        ['route' => 'stock.index',     'label' => 'Stok Gudang',      'icon' => 'box'],
        ['route' => 'stock-out.index', 'label' => 'Input Stok Keluar','icon' => 'out'],
    ];

    if ($user->role === 'director') {
        $menuItems[] = ['route' => 'profile.company', 'label' => 'Profile Perusahaan', 'icon' => 'building'];
    }

    // Inline SVG icon map
    $icons = [
        'home'     => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.5c0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75V15a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v5.25c0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75V9.75M8.25 21h8.25" />',
        'in'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 4.5-4.5m-4.5 4.5L7.5 12" />',
        'box'      => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />',
        'out'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5" />',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />',
        'logout'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />',
    ];

    $initials = strtoupper(mb_substr($user->name, 0, 1));
@endphp

{{-- ============ Sidebar (desktop) ============ --}}
<aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-blue-900 flex-col z-40">
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-blue-800/80">
        <div class="w-9 h-9 rounded-lg bg-blue-800/60 ring-1 ring-blue-700/60 flex items-center justify-center p-1.5">
            <img src="{{ asset('logo.webp') }}" alt="JadStock" class="w-full h-full object-contain">
        </div>
        <span class="text-lg font-bold text-white tracking-tight">JadStock</span>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach ($menuItems as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
                      {{ $isActive ? 'bg-blue-700 text-white' : 'text-slate-300 hover:bg-blue-800/70 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                    {!! $icons[$item['icon']] !!}
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>

{{-- ============ Main content ============ --}}
<div class="lg:ml-64 min-h-screen flex flex-col">

    {{-- Top bar --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
            {{-- Page title --}}
            <h1 class="text-base sm:text-lg font-semibold text-slate-800">@yield('title', 'Beranda')</h1>

            {{-- Avatar (direct link to Profile Saya, no dropdown) --}}
            <a href="{{ route('profile.user') }}" class="flex items-center gap-3 rounded-lg p-1.5 hover:bg-slate-100 transition">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold">
                    {{ $initials }}
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-medium text-slate-800 leading-tight">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 leading-tight">{{ $user->username }}</p>
                </div>
            </a>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-3 sm:p-6 pb-24 lg:pb-6">
        @yield('content')
    </main>
</div>

{{-- ============ Bottom bar (mobile) ============ --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-40">
    <div class="flex items-stretch justify-around">
        {{-- Beranda --}}
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-1 py-2.5 px-2 flex-1 min-w-0 transition
                  {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                {!! $icons['home'] !!}
            </svg>
            <span class="text-[10px] font-medium leading-none">Beranda</span>
        </a>

        {{-- Profile Perusahaan (director only) --}}
        @if ($user->role === 'director')
            <a href="{{ route('profile.company') }}"
               class="flex flex-col items-center gap-1 py-2.5 px-2 flex-1 min-w-0 transition
                      {{ request()->routeIs('profile.company') ? 'text-blue-600' : 'text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    {!! $icons['building'] !!}
                </svg>
                <span class="text-[10px] font-medium leading-none">Perusahaan</span>
            </a>
        @endif

        {{-- Log Out (POST form) --}}
        <form action="{{ route('logout') }}" method="POST" class="flex-1 flex">
            @csrf
            <button type="submit" class="w-full flex flex-col items-center gap-1 py-2.5 px-2 text-slate-400 hover:text-red-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    {!! $icons['logout'] !!}
                </svg>
                <span class="text-[10px] font-medium leading-none">Log Out</span>
            </button>
        </form>
    </div>
</nav>

</body>
</html>
