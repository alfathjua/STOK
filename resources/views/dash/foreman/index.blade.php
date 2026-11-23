@extends('dash.layouts.app', ['title' => 'Data Mandor'])

@section('content')

<div class="space-y-5">

    {{-- ── Header + tombol Buat Kunci ───────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-slate-800">Data Mandor</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Daftar foreman terdaftar. Buat kunci agar foreman baru bisa mendaftar.
                </p>
            </div>
            <button type="button"
                    id="btnGenerateKey"
                    onclick="generateKey()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                </svg>
                Buat Kunci
            </button>
        </div>

        {{-- ── Kunci aktif yang belum dipakai ── --}}
        @if ($activeKeys->isNotEmpty())
            <div class="mb-5">
                <p class="text-xs font-medium text-slate-500 mb-2">Kunci aktif (belum digunakan):</p>
                <div class="flex flex-wrap gap-2" id="activeKeyList">
                    @foreach ($activeKeys as $k)
                        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                            <span class="font-mono text-base font-bold text-blue-700 tracking-widest">{{ $k->key }}</span>
                            <span class="text-xs text-blue-500">{{ $k->expires_in }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── Kunci baru hasil generate (muncul via JS) ── --}}
        <div id="newKeyBox" style="display:none" class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-xs font-medium text-green-700 mb-2">Kunci baru berhasil dibuat:</p>
            <div class="flex flex-wrap items-center gap-3">
                <span id="newKeyCode" class="font-mono text-2xl font-bold text-green-700 tracking-widest"></span>
                <div class="text-xs text-green-600 space-y-0.5">
                    <p>Berlaku: <span id="newKeyExpires"></span></p>
                    <p>Dibuat: <span id="newKeyCreated"></span></p>
                </div>
                <button type="button"
                        onclick="copyKey()"
                        class="inline-flex items-center gap-1 text-xs text-green-700 border border-green-300 hover:bg-green-100 px-2.5 py-1.5 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                    </svg>
                    Salin
                </button>
            </div>
            <p class="text-xs text-green-500 mt-2">
                Bagikan kunci ini ke foreman. Kunci hanya berlaku 24 jam dan hanya bisa digunakan sekali.
            </p>
        </div>

        {{-- ── Error generate ── --}}
        <div id="keyErrorBox" style="display:none" class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"></div>

        {{-- ── Link ke halaman register ── --}}
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
            Link register foreman:
            <a href="{{ route('register') }}" target="_blank"
               class="text-blue-600 hover:underline font-medium">
                {{ route('register') }}
            </a>
        </div>
    </div>

    {{-- ── Daftar Mandor ───────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">
            Daftar Mandor
            <span class="ml-1.5 text-xs font-normal text-slate-400">({{ $foremans->count() }} orang)</span>
        </h3>

        @if ($foremans->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Belum ada mandor terdaftar</p>
                <p class="text-xs text-slate-400 mt-1">Buat kunci dan bagikan link register di atas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($foremans as $foreman)
                    <div class="border border-slate-200 rounded-xl p-4 flex items-start gap-3">

                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            @if ($foreman->avatar)
                                <img src="{{ route('private.file', ['path' => $foreman->avatar]) }}"
                                     alt="{{ $foreman->name }}"
                                     class="w-12 h-12 rounded-full object-cover border border-slate-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-lg font-bold">
                                    {{ strtoupper(mb_substr($foreman->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $foreman->name }}</p>
                            <p class="text-xs text-slate-500 truncate">&#64;{{ $foreman->username }}</p>

                            @if ($foreman->phone)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $foreman->phone) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-xs text-green-600 hover:text-green-700 mt-1 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    {{ $foreman->phone }}
                                </a>
                            @endif

                            <p class="text-[10px] text-slate-400 mt-1">
                                Bergabung {{ $foreman->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    window.generateKey = function () {
        const btn      = document.getElementById('btnGenerateKey');
        const newBox   = document.getElementById('newKeyBox');
        const errBox   = document.getElementById('keyErrorBox');

        btn.disabled   = true;
        btn.textContent = 'Membuat...';
        newBox.style.display = 'none';
        errBox.style.display = 'none';

        fetch('{{ route('foreman.generate-key') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('newKeyCode').textContent    = data.key;
                document.getElementById('newKeyExpires').textContent = data.expires_in;
                document.getElementById('newKeyCreated').textContent = data.created_at;
                newBox.style.display = 'block';
            } else {
                errBox.textContent   = data.message ?? 'Gagal membuat kunci.';
                errBox.style.display = 'block';
            }
        })
        .catch(() => {
            errBox.textContent   = 'Terjadi kesalahan. Coba lagi.';
            errBox.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" /></svg> Buat Kunci`;
        });
    };

    window.copyKey = function () {
        const key = document.getElementById('newKeyCode').textContent;
        navigator.clipboard?.writeText(key).then(() => {
            alert('Kunci "' + key + '" berhasil disalin!');
        });
    };
})();
</script>

@endsection
