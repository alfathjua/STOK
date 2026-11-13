@extends('dash.layouts.app', ['title' => 'Profile Perusahaan'])

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Header --}}
        <div class="bg-blue-900 px-6 py-8 flex items-center gap-5">
            <div class="w-16 h-16 rounded-xl bg-blue-800/60 ring-1 ring-blue-700/60 flex items-center justify-center p-3">
                <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">CV. Jada Team Sedaya</h2>
                <p class="text-blue-200 text-sm">Profile Perusahaan</p>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Nama Perusahaan</label>
                <p class="text-sm text-slate-800">CV. Jada Team Sedaya</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Aplikasi</label>
                <p class="text-sm text-slate-800">JadStock</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Alamat</label>
                <p class="text-sm text-slate-500 italic">Belum diatur. Silakan lengkapi data perusahaan.</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Peta Lokasi</label>
                <p class="text-sm text-slate-500 italic">Belum diatur.</p>
            </div>
        </div>
    </div>
</div>

@endsection
