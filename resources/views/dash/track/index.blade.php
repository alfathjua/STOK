@extends('dash.layouts.app', ['title' => 'Lacak Aktivitas'])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Lacak Aktivitas</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Rekam jejak seluruh aksi yang dilakukan foreman di sistem.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('track.index') }}" class="flex flex-wrap gap-3 mb-5">
            <select name="user_id"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition bg-white text-slate-700">
                <option value="">Semua pengguna</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>

            <select name="action"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition bg-white text-slate-700">
                <option value="">Semua aksi</option>
                <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Tambah</option>
                <option value="edit" {{ request('action') === 'edit' ? 'selected' : '' }}>Edit</option>
                <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Hapus</option>
            </select>

            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                Filter
            </button>

            @if (request()->hasAny(['user_id', 'action']))
                <a href="{{ route('track.index') }}"
                    class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-slate-600 transition px-2">
                    Reset
                </a>
            @endif
        </form>

        @include('dash.track.partials.list', ['logs' => $logs])
    </div>
@endsection
