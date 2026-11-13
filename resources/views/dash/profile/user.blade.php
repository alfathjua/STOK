@extends('dash.layouts.app', ['title' => 'Profile Saya'])

@php $user = auth()->user(); @endphp

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        {{-- Header --}}
        <div class="bg-blue-900 px-6 py-8 flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-blue-700 ring-2 ring-blue-500/40 flex items-center justify-center text-2xl font-bold text-white">
                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-blue-200 text-sm">{{ $user->username }}</p>
                <span class="inline-block mt-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-blue-700/60 text-blue-100 capitalize">
                    {{ $user->role }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Nama Lengkap</label>
                <p class="text-sm text-slate-800">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Username</label>
                <p class="text-sm text-slate-800">{{ $user->username }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Email</label>
                <p class="text-sm text-slate-800">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Role</label>
                <p class="text-sm text-slate-800 capitalize">{{ $user->role }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
