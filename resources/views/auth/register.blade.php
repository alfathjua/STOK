<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mandor &mdash; JadStock</title>
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .fade-in-d1 {
            animation: fadeIn 0.6s ease-out 0.15s both;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    <div class="min-h-screen flex">

        <div class="hidden lg:flex lg:w-1/2 bg-blue-900 flex-col items-center justify-center px-12">
            <div
                class="fade-in w-28 h-28 rounded-2xl bg-blue-800/60 ring-1 ring-blue-700/60 flex items-center justify-center p-5 mb-7">
                <img src="{{ asset('logo.webp') }}" alt="JadStock" class="w-full h-full object-contain">
            </div>
            <h1 class="fade-in text-5xl font-bold text-white tracking-tight mb-3">JadStock</h1>
            <p class="fade-in-d1 text-slate-300 text-base tracking-wide">Daftar sebagai Mandor / Foreman</p>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 py-10 sm:px-12 overflow-y-auto">

            <div class="lg:hidden mb-7 flex flex-col items-center">
                <div
                    class="w-16 h-16 rounded-xl bg-blue-800 ring-1 ring-blue-700 flex items-center justify-center p-3 mb-3">
                    <img src="{{ asset('logo.webp') }}" alt="JadStock" class="w-full h-full object-contain">
                </div>
                <span class="text-xl font-bold text-blue-900">JadStock</span>
            </div>

            <div class="w-full max-w-sm">
                <h2 class="text-2xl font-bold text-slate-800 mb-1">Daftar Mandor</h2>
                <p class="text-sm text-slate-500 mb-6">Masukkan kunci yang diberikan direktur untuk mendaftar.</p>

                @if ($errors->any())
                    <div
                        class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 flex-shrink-0 mt-0.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('register.store') }}" method="POST" class="space-y-4" id="registerForm">
                    @csrf

                    <div>
                        <label for="key" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Kunci Registrasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="key" name="key" value="{{ old('key') }}" maxlength="6"
                            placeholder="Contoh: AB3X9Z" autocomplete="off"
                            style="text-transform:uppercase; letter-spacing:0.2em"
                            class="w-full px-4 py-2.5 border rounded-lg text-slate-800 font-mono text-center text-lg
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                  focus:border-blue-500 transition @error('key') border-red-400 @else border-slate-300 @enderror">
                        @error('key')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Nama lengkap Anda"
                            class="w-full px-4 py-2.5 border rounded-lg text-slate-800 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition
                                  @error('name') border-red-400 @else border-slate-300 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                            placeholder="username_anda" autocomplete="username"
                            class="w-full px-4 py-2.5 border rounded-lg text-slate-800 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition
                                  @error('username') border-red-400 @else border-slate-300 @enderror"
                            oninput="checkUsername(this.value)">
                        <p id="usernameStatus" class="text-xs mt-1 hidden"></p>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" placeholder="Min. 8 karakter"
                                autocomplete="new-password"
                                class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-800
                                      placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                      focus:border-blue-500 transition"
                                oninput="checkPasswordMatch()">
                            <button type="button" onclick="togglePwd('password', 'eyePass')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition">
                                <svg id="eyePass" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
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
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="Ulangi password" autocomplete="new-password"
                                class="w-full pl-4 pr-12 py-2.5 border border-slate-300 rounded-lg text-slate-800
                                      placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40
                                      focus:border-blue-500 transition"
                                oninput="checkPasswordMatch()">
                            <button type="button" onclick="togglePwd('password_confirmation', 'eyeConfirm')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition">
                                <svg id="eyeConfirm" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                        <p id="pwdMatchStatus" class="text-xs mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Verifikasi <span class="text-red-500">*</span>
                        </label>
                        <div id="sliderCaptcha"
                            class="relative select-none rounded-lg overflow-hidden border border-slate-300 bg-slate-100"
                            style="height:48px">
                            {{-- Track --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span id="sliderText"
                                    class="text-sm text-slate-400 font-medium tracking-wide transition-opacity duration-300">
                                    Geser ke kanan untuk memverifikasi
                                </span>
                            </div>
                            <div id="sliderFill"
                                class="absolute inset-y-0 left-0 w-0 bg-blue-100 transition-colors duration-300 rounded-l-lg">
                            </div>
                            <div id="sliderHandle"
                                class="absolute inset-y-0 left-0 w-12 flex items-center justify-center
                                    bg-blue-600 hover:bg-blue-700 text-white rounded-lg cursor-grab active:cursor-grabbing
                                    shadow transition-colors duration-150 z-10"
                                style="touch-action:none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5 pointer-events-none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" id="captchaPassed" name="_captcha" value="0">
                        <p id="captchaError" class="text-xs text-red-500 mt-1 hidden">Selesaikan verifikasi terlebih
                            dahulu.</p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition mt-2">
                        Daftar
                    </button>
                </form>

                <p class="text-center text-xs text-slate-400 mt-5">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Masuk</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        (function() {
            let usernameTimer = null;

            window.checkUsername = function(value) {
                const statusEl = document.getElementById('usernameStatus');
                clearTimeout(usernameTimer);

                if (value.length < 3) {
                    statusEl.classList.add('hidden');
                    return;
                }

                usernameTimer = setTimeout(function() {
                    fetch('{{ route('register.check-username') }}?username=' + encodeURIComponent(value), {
                            headers: {
                                'Accept': 'application/json'
                            },
                        })
                        .then(r => r.json())
                        .then(data => {
                            statusEl.classList.remove('hidden');
                            if (data.available) {
                                statusEl.className = 'text-xs mt-1 text-green-600';
                                statusEl.textContent = '✓ Username tersedia';
                            } else {
                                statusEl.className = 'text-xs mt-1 text-red-500';
                                statusEl.textContent = '✕ Username sudah digunakan oleh foreman lain';
                            }
                        });
                }, 400);
            };

            window.checkPasswordMatch = function() {
                const pwd = document.getElementById('password').value;
                const confirm = document.getElementById('password_confirmation').value;
                const statusEl = document.getElementById('pwdMatchStatus');

                if (confirm.length === 0) {
                    statusEl.classList.add('hidden');
                    return;
                }

                statusEl.classList.remove('hidden');
                if (pwd === confirm) {
                    statusEl.className = 'text-xs mt-1 text-green-600';
                    statusEl.textContent = '✓ Password cocok';
                } else {
                    statusEl.className = 'text-xs mt-1 text-red-500';
                    statusEl.textContent = '✕ Password tidak cocok';
                }
            };

            const eyeOpen =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            const eyeClosed =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';

            window.togglePwd = function(inputId, iconId) {
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

            (function() {
                const track = document.getElementById('sliderCaptcha');
                const handle = document.getElementById('sliderHandle');
                const fill = document.getElementById('sliderFill');
                const text = document.getElementById('sliderText');
                const input = document.getElementById('captchaPassed');

                const HANDLE_W = 48;
                const THRESHOLD = 0.92;

                let dragging = false;
                let startX = 0;
                let currentX = 0;

                function trackWidth() {
                    return track.offsetWidth - HANDLE_W;
                }

                function setPos(x) {
                    const max = trackWidth();
                    const clamped = Math.max(0, Math.min(x, max));
                    handle.style.left = clamped + 'px';
                    fill.style.width = (clamped + HANDLE_W) + 'px';
                    return clamped;
                }

                function onSuccess() {
                    handle.style.left = trackWidth() + 'px';
                    fill.style.width = '100%';
                    fill.classList.remove('bg-blue-100');
                    fill.classList.add('bg-green-200');
                    handle.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    handle.classList.add('bg-green-600', 'cursor-default');
                    handle.innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 pointer-events-none"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                    text.textContent = '✓ Verifikasi berhasil';
                    text.classList.remove('text-slate-400');
                    text.classList.add('text-green-600');
                    input.value = '1';
                    document.getElementById('captchaError').classList.add('hidden');
                    dragging = false;
                }

                function onReset() {
                    setPos(0);
                    fill.classList.add('bg-blue-100');
                    fill.classList.remove('bg-green-200');
                    handle.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    handle.classList.remove('bg-green-600', 'cursor-default');
                    handle.innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 pointer-events-none"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>';
                    text.textContent = 'Geser ke kanan untuk memverifikasi';
                    text.classList.add('text-slate-400');
                    text.classList.remove('text-green-600');
                    input.value = '0';
                }

                handle.addEventListener('mousedown', function(e) {
                    if (input.value === '1') {
                        return;
                    }
                    dragging = true;
                    startX = e.clientX - currentX;
                    e.preventDefault();
                });

                document.addEventListener('mousemove', function(e) {
                    if (!dragging) {
                        return;
                    }
                    currentX = setPos(e.clientX - startX);
                    if (currentX / trackWidth() >= THRESHOLD) {
                        onSuccess();
                    }
                });

                document.addEventListener('mouseup', function() {
                    if (!dragging) {
                        return;
                    }
                    dragging = false;
                    if (input.value !== '1') {
                        currentX = 0;
                        onReset();
                    }
                });

                handle.addEventListener('touchstart', function(e) {
                    if (input.value === '1') {
                        return;
                    }
                    dragging = true;
                    startX = e.touches[0].clientX - currentX;
                }, {
                    passive: true
                });

                document.addEventListener('touchmove', function(e) {
                    if (!dragging) {
                        return;
                    }
                    currentX = setPos(e.touches[0].clientX - startX);
                    if (currentX / trackWidth() >= THRESHOLD) {
                        onSuccess();
                    }
                }, {
                    passive: true
                });

                document.addEventListener('touchend', function() {
                    if (!dragging) {
                        return;
                    }
                    dragging = false;
                    if (input.value !== '1') {
                        currentX = 0;
                        onReset();
                    }
                });
            })();

            document.getElementById('registerForm').addEventListener('submit', function(e) {
                if (document.getElementById('captchaPassed').value !== '1') {
                    e.preventDefault();
                    document.getElementById('captchaError').classList.remove('hidden');
                }
            });
            document.getElementById('key').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        })();
    </script>

</body>

</html>
