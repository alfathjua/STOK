@extends('dash.layouts.app', ['title' => 'Profil Saya'])

@section('content')

    @php $user = auth()->user(); @endphp

    <div class="max-w-2xl mx-auto space-y-5">

        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Informasi Profil</h3>

            @if ($errors->hasBag('default') || $errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <p class="font-medium mb-1">Periksa kembali:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update-info') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div id="infoAvatarInitials"
                                class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xl font-bold {{ $user->avatar ? 'hidden' : '' }}">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <img id="infoAvatarPreview"
                                src="{{ $user->avatar ? route('private.file', ['path' => $user->avatar]) : '' }}"
                                alt="Foto profil"
                                class="w-14 h-14 rounded-full object-cover border border-slate-200 {{ $user->avatar ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp"
                                class="hidden" onchange="previewAvatar(this)">
                            <button type="button" onclick="document.getElementById('avatarInput').click()"
                                class="inline-flex items-center gap-2 border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-3 py-2 rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                                Pilih Foto
                            </button>
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WebP — maks. 4 MB</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                </div>

                <div>
                    <label for="edit_username" class="block text-sm font-medium text-slate-700 mb-1.5">Username <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="edit_username" name="username" value="{{ old('username', $user->username) }}"
                        autocomplete="off"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                    <p id="usernameStatus" class="text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Nomor WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="Contoh: 08123456789"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-slate-800 placeholder-slate-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition">
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Ganti Password</h3>

            <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password"
                            placeholder="Masukkan password saat ini"
                            class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-800
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                  focus:border-blue-500 transition"
                            oninput="checkCurrentPassword()">
                        <button type="button" onclick="togglePwdField('current_password', 'eyeCurr')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition">
                            <svg id="eyeCurr" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    <p id="currentPwdStatus" class="text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="password"
                            placeholder="Min. 8 karakter, huruf + angka"
                            class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-800
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                  focus:border-blue-500 transition"
                            oninput="checkPwdConfirm()">
                        <button type="button" onclick="togglePwdField('new_password', 'eyeNew')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition">
                            <svg id="eyeNew" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi password baru"
                            class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-800
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                  focus:border-blue-500 transition"
                            oninput="checkPwdConfirm()">
                        <button type="button" onclick="togglePwdField('password_confirmation', 'eyeConf')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition">
                            <svg id="eyeConf" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    <p id="pwdConfirmStatus" class="text-xs mt-1 hidden"></p>
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="bg-slate-700 hover:bg-slate-800 text-white font-medium px-6 py-2.5 rounded-lg transition text-sm">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        (function() {
            window.previewAvatar = function(input) {
                if (!input.files || !input.files[0]) {
                    return;
                }
                const url = URL.createObjectURL(input.files[0]);
                const headerInitials = document.getElementById('avatarInitials');
                const headerPreview = document.getElementById('avatarPreview');
                if (headerInitials) {
                    headerInitials.classList.add('hidden');
                }
                if (headerPreview) {
                    headerPreview.src = url;
                    headerPreview.classList.remove('hidden');
                }

                const formInitials = document.getElementById('infoAvatarInitials');
                const formPreview = document.getElementById('infoAvatarPreview');
                if (formInitials) {
                    formInitials.classList.add('hidden');
                }
                if (formPreview) {
                    formPreview.src = url;
                    formPreview.classList.remove('hidden');
                }
            };

            const usernameInput = document.getElementById('edit_username');
            const usernameStatus = document.getElementById('usernameStatus');
            const checkUrl = '{{ route('profile.check-username') }}';
            const originalUsername = usernameInput ? usernameInput.defaultValue : '';
            let usernameTimer = null;

            function setUsernameStatus(type, text) {
                usernameStatus.classList.remove('hidden', 'text-green-600', 'text-red-500', 'text-slate-400');
                usernameStatus.classList.add(type === 'ok' ? 'text-green-600' : type === 'err' ? 'text-red-500' :
                    'text-slate-400');
                usernameStatus.textContent = text;
            }

            if (usernameInput) {
                usernameInput.addEventListener('input', function() {
                    clearTimeout(usernameTimer);
                    const val = this.value.trim();

                    if (val === originalUsername || val.length < 3) {
                        usernameStatus.classList.add('hidden');
                        return;
                    }

                    setUsernameStatus('info', 'Memeriksa username…');

                    usernameTimer = setTimeout(async function() {
                        try {
                            const res = await fetch(checkUrl + '?username=' + encodeURIComponent(
                                val));
                            const data = await res.json();
                            if (data.available) {
                                setUsernameStatus('ok', '✓ Username tersedia');
                            } else {
                                setUsernameStatus('err',
                                    '✕ Username sudah digunakan, coba yang lain');
                            }
                        } catch (e) {
                            usernameStatus.classList.add('hidden');
                        }
                    }, 450);
                });
            }

            const currentPwdInput = document.getElementById('current_password');
            const currentPwdStatus = document.getElementById('currentPwdStatus');
            const checkPwdUrl = '{{ route('profile.check-password') }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                '{{ csrf_token() }}';
            let pwdTimer = null;

            function setCurrentPwdStatus(type, text) {
                currentPwdStatus.classList.remove('hidden', 'text-green-600', 'text-red-500', 'text-slate-400');
                currentPwdStatus.classList.add(type === 'ok' ? 'text-green-600' : type === 'err' ? 'text-red-500' :
                    'text-slate-400');
                currentPwdStatus.textContent = text;
            }

            window.checkCurrentPassword = function() {
                clearTimeout(pwdTimer);
                const val = currentPwdInput.value;

                if (val.length === 0) {
                    currentPwdStatus.classList.add('hidden');
                    return;
                }

                setCurrentPwdStatus('info', 'Memeriksa password…');

                pwdTimer = setTimeout(async function() {
                    try {
                        const res = await fetch(checkPwdUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                current_password: val
                            }),
                        });
                        const data = await res.json();
                        if (data.valid) {
                            setCurrentPwdStatus('ok', '✓ Password benar');
                        } else {
                            setCurrentPwdStatus('err', '✕ Password salah');
                        }
                    } catch (e) {
                        currentPwdStatus.classList.add('hidden');
                    }
                }, 500);
            };

            window.checkPwdConfirm = function() {
                const pwd = document.getElementById('new_password').value;
                const confirm = document.getElementById('password_confirmation').value;
                const status = document.getElementById('pwdConfirmStatus');

                if (confirm.length === 0) {
                    status.classList.add('hidden');
                    return;
                }
                status.classList.remove('hidden');

                if (pwd === confirm) {
                    status.className = 'text-xs mt-1 text-green-600';
                    status.textContent = '✓ Password cocok';
                } else {
                    status.className = 'text-xs mt-1 text-red-500';
                    status.textContent = '✕ Password tidak cocok';
                }
            };

            const eyeOpen =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            const eyeClosed =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';

            window.togglePwdField = function(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = eyeClosed;
                } else {
                    input.type = 'password';
                    icon.innerHTML = eyeOpen;
                }
            };
        })();
    </script>

@endsection
