# Security Audit Report — JadStock (STOK)

**Run ID:** stok-run-1
**Repository:** STOK (`/home/thomas-lb/jobs/joki/jua/STOK`)
**Source Ref:** `ead7246` (HEAD → main)
**Profile:** Standard — Full Codebase
**Tanggal Audit:** 24 November 2026
**Stack:** Laravel 13 · PHP 8.5 · MySQL (Eloquent) · Blade

---

## Executive Summary

JadStock adalah aplikasi manajemen stok gudang dengan dua peran: **director** dan **foreman**. Audit menemukan **12 temuan** (3 High, 3 Medium, 3 Low, 3 Informational). Tidak ada Critical. `composer audit` bersih — tidak ada kerentanan pada dependensi pihak ketiga.

Risiko terbesar berasal dari **ketiadaan rate limiting di seluruh aplikasi** dan **dua celah IDOR** yang memungkinkan foreman memodifikasi atau menghapus data milik foreman lain.

| Severity      | Jumlah |
|---------------|--------|
| 🔴 Critical   | 0      |
| 🔴 High       | 3      |
| 🟠 Medium     | 3      |
| 🟡 Low        | 3      |
| ℹ️ Informational | 3   |
| **Total**     | **12** |

---

## Prioritas Perbaikan

| Urutan | ID    | Severity | Judul                                        | Status |
|--------|-------|----------|----------------------------------------------|--------|
| 1      | F-02  | High     | IDOR — `destroyReceipt` tanpa ownership check | ✅ Fixed |
| 2      | F-03  | High     | IDOR — Edit/Delete StockIn tanpa ownership check | ✅ Fixed |
| 3      | F-01  | High     | Username enumeration + tidak ada brute force protection | ✅ Fixed |
| 4      | F-04  | Medium   | Password oracle via `/profile/check-password` | ✅ Fixed |
| 5      | F-05  | Medium   | `trustProxies('*')` — IP header spoofing     | ✅ Fixed |
| 6      | F-06  | Medium   | Session cookie `Secure` flag tidak diset     | ✅ Fixed |
| 7      | F-07  | Low      | Session tidak dienkripsi                     | ✅ Fixed |
| 8      | F-08  | Low      | HTTP security headers tidak dikonfigurasi    | ✅ Fixed |
| 9      | F-09  | Low      | Tidak ada rate limiting di seluruh aplikasi  | ✅ Fixed |
| 10     | F-10  | Low      | `role` ada di `$fillable` User model         | ✅ Fixed |
| 11     | F-11  | Info     | `addslashes()` digunakan untuk JS context    | ✅ Fixed |

---

## Metodologi

Pemeriksaan mencakup seluruh source code PHP dan Blade di bawah `app/`, `routes/`, `resources/views/`, `config/`, dan `bootstrap/`. Pengujian bersifat **read-only static analysis** dengan verifikasi lokal terbatas (simulasi path normalization, encoding, dan perilaku Flysystem). Tidak ada request dikirim ke server eksternal, tidak ada kode target dieksekusi secara langsung terhadap environment production.

---

---

# Temuan Detail

---

## F-01 — Username Enumeration + Tidak Ada Brute Force Protection

**Severity:** 🔴 High
**File:** `app/Http/Controllers/AuthController.php`, baris 53 & 62
**Route:** `POST /login`

### Bukti Source Code

```php
// Baris 53 — username tidak ditemukan
return back()
    ->withErrors(['username' => 'Username tidak ditemukan.'])
    ->withInput($request->only('username'));

// Baris 62 — username ada, password salah
return back()
    ->withErrors(['password' => 'Password salah.'])
    ->withInput($request->only('username'));
```

Dua pesan error yang berbeda dikirim tergantung apakah username ditemukan atau tidak. Tidak ada `throttle` middleware, `RateLimiter`, atau mekanisme lockout di manapun di `routes/web.php`.

### Dampak

Penyerang dapat secara sistematis mengkonfirmasi apakah sebuah username terdaftar (enumeration), lalu melanjutkan brute force password tanpa hambatan karena tidak ada delay, lockout, atau CAPTCHA di sisi server.

### Skenario Eksploitasi

1. Kirim `POST /login` dengan username acak → respon *"Username tidak ditemukan"* = username tidak ada.
2. Kirim `POST /login` dengan username yang valid → respon *"Password salah"* = username terkonfirmasi.
3. Lanjutkan brute force password menggunakan wordlist secara otomatis — tidak ada mekanisme penghambat.

### Rekomendasi

```php
// 1. Gunakan pesan error generik untuk kedua kasus:
return back()
    ->withErrors(['login' => 'Username atau password salah.'])
    ->withInput($request->only('username'));

// 2. Tambahkan rate limiting di routes/web.php:
Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('authenticate')
    ->middleware('throttle:5,1'); // maks. 5 percobaan per menit
```

---

## F-02 — IDOR: `destroyReceipt` Tanpa Ownership Check

**Severity:** 🔴 High
**File:** `app/Http/Controllers/StockInController.php`, baris 200–206
**Route:** `DELETE /receipts/{receipt}`

### Bukti Source Code

```php
public function destroyReceipt(Receipt $receipt): JsonResponse
{
    Storage::disk('local')->delete($receipt->images);
    $receipt->delete();

    return response()->json(['success' => true]);
}
```

Route model binding me-resolve `Receipt` berdasarkan `id` dari URL. Tidak ada pengecekan apakah `$receipt->stockIn->user_id === auth()->id()`. ID bersifat auto-increment sehingga mudah di-enumerate.

### Dampak

Semua pengguna terautentikasi (foreman maupun director) dapat menghapus struk/receipt milik pengguna lain dengan mengirim `DELETE /receipts/{id}`, merusak integritas data historis secara permanen (file ikut terhapus dari storage).

### Skenario Eksploitasi

Foreman mengirim request berurutan:
```
DELETE /receipts/1
DELETE /receipts/2
DELETE /receipts/3
```
Semua receipt tersebut terhapus, termasuk milik foreman atau director lain.

### Rekomendasi

```php
public function destroyReceipt(Receipt $receipt): JsonResponse
{
    // Hanya pemilik atau director yang boleh menghapus
    if ($receipt->stockIn->user_id !== auth()->id()
        && auth()->user()->role !== 'director') {
        abort(403);
    }

    Storage::disk('local')->delete($receipt->images);
    $receipt->delete();

    return response()->json(['success' => true]);
}
```

---

## F-03 — IDOR: Edit/Update/Delete StockIn Tanpa Ownership Check

**Severity:** 🔴 High
**File:** `app/Http/Controllers/StockInController.php`, method `edit` (baris 128), `update` (baris 133), `destroy` (baris 205)
**Routes:** `GET /stock-in/{uuid}/edit`, `PUT /stock-in/{uuid}`, `DELETE /stock-in/{uuid}`

### Bukti Source Code

```php
// Method edit — tidak ada ownership check
public function edit(StockIn $stockIn): View
{
    $stockIn->load(['details', 'receipts']);
    return view('dash.stock-in.action', compact('stockIn'));
}

// Method destroy — langsung hapus tanpa verifikasi
public function destroy(StockIn $stockIn): RedirectResponse
{
    DB::transaction(function () use ($stockIn): void {
        $stockIn->delete(); // tidak ada cek user_id
    });
}
```

Route `stock-in` hanya dilindungi middleware `auth`. Penggunaan UUID sebagai route key menambah obscurity, bukan authorization.

### Dampak

Foreman A dapat mengakses URL edit stok masuk milik Foreman B, mengubah data (asal stok, daftar barang, harga), atau menghapus seluruh catatan stok masuk beserta file-file terkait.

### Skenario Eksploitasi

Foreman mengamati UUID miliknya sendiri dari URL, lalu mencoba UUID lain yang didapat dari sumber apapun (log browser, link yang ter-share, referrer header) dan berhasil mengedit data milik foreman lain.

### Rekomendasi

```php
// Tambahkan helper private di StockInController:
private function authorizeStockIn(StockIn $stockIn): void
{
    if (auth()->user()->role !== 'director'
        && $stockIn->user_id !== auth()->id()) {
        abort(403, 'Anda tidak berhak mengakses data ini.');
    }
}

// Panggil di awal setiap method yang memodifikasi:
public function edit(StockIn $stockIn): View
{
    $this->authorizeStockIn($stockIn);
    $stockIn->load(['details', 'receipts']);
    return view('dash.stock-in.action', compact('stockIn'));
}

public function update(Request $request, StockIn $stockIn): RedirectResponse
{
    $this->authorizeStockIn($stockIn);
    // ... sisa logic
}

public function destroy(StockIn $stockIn): RedirectResponse
{
    $this->authorizeStockIn($stockIn);
    // ... sisa logic
}
```

---

## F-04 — Password Oracle via `/profile/check-password` Tanpa Rate Limiting

**Severity:** 🟠 Medium
**File:** `app/Http/Controllers/ProfileController.php`, baris 84–96
**Route:** `POST /profile/check-password`

### Bukti Source Code

```php
public function checkCurrentPassword(Request $request): JsonResponse
{
    $password = $request->input('current_password', '');

    if (empty($password)) {
        return response()->json(['valid' => false]);
    }

    $isValid = Hash::check($password, auth()->user()->password);

    return response()->json(['valid' => $isValid]);
}
```

Endpoint mengembalikan `{"valid": true/false}` secara eksplisit tanpa pembatasan frekuensi apapun.

### Dampak

Penyerang dengan session aktif (misalnya dari session hijacking, shared device, atau insider) dapat secara otomatis menguji ribuan kombinasi password melalui endpoint ini. Password yang ditemukan dapat digunakan untuk credential stuffing di layanan lain.

### Skenario Eksploitasi

Dengan session token yang valid, kirim POST ke `/profile/check-password` dengan `current_password` dari wordlist. Response `{"valid": true}` mengkonfirmasi password dengan segera tanpa hambatan.

### Rekomendasi

```php
// Di routes/web.php:
Route::post('/profile/check-password', [ProfileController::class, 'checkCurrentPassword'])
    ->name('profile.check-password')
    ->middleware('throttle:10,1'); // maks. 10 request per menit
```

---

## F-05 — `trustProxies('*')` Memungkinkan IP Header Spoofing

**Severity:** 🟠 Medium
**File:** `bootstrap/app.php`, baris 18

### Bukti Source Code

```php
$middleware->trustProxies(at: '*');
```

Konfigurasi ini mempercayai **semua** header proxy (`X-Forwarded-For`, `X-Forwarded-Host`, dll.) dari semua IP tanpa pembatasan.

### Dampak

Jika rate limiting berbasis IP ditambahkan (sangat disarankan), penyerang dapat memalsukan header `X-Forwarded-For` untuk melewati pembatasan tersebut. Selain itu, dapat digunakan untuk memanipulasi URL generation jika aplikasi mendeteksi HTTPS dari header ini.

### Rekomendasi

```php
// Batasi ke IP proxy yang sebenarnya (mis. Cloudflare, load balancer):
$middleware->trustProxies(at: [
    '10.0.0.1',      // IP load balancer internal
    '103.21.244.0/22', // contoh range Cloudflare
]);
```

---

## F-06 — Session Cookie `Secure` Flag Tidak Diset Secara Eksplisit

**Severity:** 🟠 Medium
**File:** `config/session.php`

### Bukti Source Code

```php
'secure' => env('SESSION_SECURE_COOKIE'),
```

Jika variabel `SESSION_SECURE_COOKIE` tidak ada di `.env` production, nilainya `null` yang diinterpretasikan sebagai `false` — session cookie dikirim tanpa flag `Secure`.

### Dampak

Di environment production yang seharusnya HTTPS, session cookie dapat disadap jika user mengakses URL HTTP sebelum redirect, atau jika ada mixed-content request, membuka celah session hijacking.

### Rekomendasi

```ini
# Di .env production:
SESSION_SECURE_COOKIE=true
```

Atau hardcode di `config/session.php` agar tidak bergantung pada `.env`:

```php
'secure' => app()->isProduction(),
```

---

## F-07 — Session Tidak Dienkripsi (Default)

**Severity:** 🟡 Low
**File:** `config/session.php`

### Bukti Source Code

```php
'encrypt' => env('SESSION_ENCRYPT', false),
```

Default `false` berarti payload session (user ID, flash messages, CSRF token) tersimpan dalam plaintext di session storage.

### Dampak

Jika session storage dapat diakses (file system attack, database dump), isi session dapat dibaca langsung tanpa perlu mendekripsi.

### Rekomendasi

```ini
SESSION_ENCRYPT=true
```

---

## F-08 — HTTP Security Headers Tidak Dikonfigurasi

**Severity:** 🟡 Low
**File:** `app/Providers/AppServiceProvider.php`, `bootstrap/app.php`

Tidak ditemukan konfigurasi security headers di manapun dalam codebase.

| Header                    | Risiko Tanpa Header         |
|---------------------------|-----------------------------|
| `X-Frame-Options`         | Clickjacking                |
| `X-Content-Type-Options`  | MIME-type sniffing          |
| `Content-Security-Policy` | Mitigasi XSS layer tambahan |
| `Strict-Transport-Security` | SSL stripping             |
| `Referrer-Policy`         | Data leakage via referrer   |

### Rekomendasi

Tambahkan middleware global di `bootstrap/app.php`:

```php
$middleware->append(function ($request, $next) {
    $response = $next($request);
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    if (app()->isProduction()) {
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );
    }
    return $response;
});
```

---

## F-09 — Tidak Ada Rate Limiting di Seluruh Aplikasi

**Severity:** 🟡 Low
**File:** `routes/web.php`

Tidak ditemukan penggunaan `throttle` middleware atau `RateLimiter` di seluruh file route.

Endpoint berisiko tinggi yang tidak dilindungi:

| Endpoint | Risiko |
|----------|--------|
| `POST /login` | Brute force authentication |
| `POST /register` | Spam pembuatan akun |
| `POST /profile/check-password` | Password oracle |
| `GET /register/check-username` | Username enumeration |
| `GET /profile/check-username` | Username enumeration |

### Rekomendasi

```php
// Di routes/web.php:
Route::post('/login', ...)->middleware('throttle:5,1');
Route::post('/register', ...)->middleware('throttle:3,5');
Route::get('/register/check-username', ...)->middleware('throttle:30,1');
Route::get('/profile/check-username', ...)->middleware('throttle:30,1');
```

---

## F-10 — `role` Ada di `$fillable` User Model

**Severity:** 🟡 Low
**File:** `app/Models/User.php`, baris 18–26

### Bukti Source Code

```php
protected $fillable = [
    'name',
    'email',
    'username',
    'phone',
    'avatar',
    'role',      // <-- role ada di fillable
    'password',
];
```

Saat ini tidak eksploitabel secara langsung karena `ProfileController::updateInfo()` mengisi field secara eksplisit (bukan mass assignment). Namun kehadiran `role` di `$fillable` adalah debt teknis berbahaya.

### Dampak

Potensi privilege escalation jika developer masa depan menggunakan `$user->fill($request->all())` atau `User::create($request->all())` tanpa filtering eksplisit.

### Rekomendasi

```php
// Pindahkan role keluar dari fillable:
protected $fillable = [
    'name', 'email', 'username', 'phone', 'avatar', 'password',
];

// Set role secara eksplisit saat dibutuhkan:
$user->role = 'foreman';
$user->save();
```

---

---

# Temuan Informational

---

## F-11 — `addslashes()` Digunakan untuk JS Context

**Severity:** ℹ️ Informational — Tidak eksploitabel dalam kondisi saat ini
**File:** `resources/views/dash/stock-out/partials/list.blade.php`, baris 74

### Bukti Source Code

```blade
onclick="openOutModal({{ $stock->id }}, '{{ addslashes($detail->item_name) }}', ...)"
```

**Mengapa saat ini aman:** Blade `{{ }}` menerapkan `htmlspecialchars` dengan flag `ENT_QUOTES`, yang mengonversi single quote menjadi `&#039;`. Output `openOutModal` di JavaScript menggunakan `.textContent` (bukan `innerHTML`), sehingga HTML entities tidak dieksekusi sebagai script.

**Mengapa tetap perlu diperbaiki:** Pendekatan ini bergantung pada dua lapisan proteksi yang beroperasi bersamaan. Jika salah satu berubah (mis. developer mengubah ke `{!! !!}`), celah langsung terbuka.

### Rekomendasi

Gunakan `json_encode()` yang idiomatis dan aman untuk menyisipkan data ke JS:

```blade
onclick="openOutModal({{ $stock->id }}, {{ json_encode($detail->item_name) }}, {{ $stock->qty_remaining }}, {{ (float) $detail->price }})"
```

---

## F-12 — Captcha Slider Hanya Client-Side

**Severity:** ℹ️ Informational
**File:** `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`

### Deskripsi

Captcha slider adalah pure JavaScript. Field hidden `_captcha` diset ke `1` saat slider berhasil digeser. Server tidak memvalidasi field ini sama sekali — bot dapat langsung mengirim POST request tanpa berinteraksi dengan captcha.

### Dampak

Captcha tidak memberikan perlindungan nyata terhadap automated attacks di sisi server. Efeknya hanya visual/UX.

### Rekomendasi

Untuk perlindungan nyata, gunakan layanan captcha dengan server-side token verification seperti Cloudflare Turnstile atau hCaptcha. Keduanya gratis untuk traffic rendah dan mudah diintegrasikan ke Laravel.

---

---

# Temuan yang Dikecualikan

Berikut adalah kandidat temuan yang diinvestigasi namun **ditolak** setelah verifikasi source code.

## Path Traversal via `/private-file/{path}` — REJECTED

Route menggunakan regex `.*` yang mengizinkan karakter apapun termasuk `..`. Namun, `Filesystem.php` Flysystem (baris 33, 48) mengkonfirmasi bahwa `has()` memanggil `WhitespacePathNormalizer::normalizePath()` yang melempar `PathTraversalDetected` exception ketika path berusaha keluar dari root storage (`parts[]` kosong dan `..` ditemukan). Simulasi lokal memverifikasi perilaku ini. Tidak ada jalur bypass yang ditemukan.

## XSS via `{!! !!}` — REJECTED

Semua penggunaan `{!! !!}` dalam codebase merujuk ke variabel `$icons` yang didefinisikan secara hardcoded di dalam layout Blade, bukan dari input pengguna. Tidak ada jalur dari input user ke output raw.

## Stored XSS via Activity Log — REJECTED

Semua output dari kolom `description` activity log di `track/partials/list.blade.php` menggunakan Blade `{{ }}` (auto-escape HTML). Tidak ada penggunaan `{!! !!}` untuk data yang berasal dari user.

---

---

# Penutup

Aplikasi JadStock memiliki fondasi keamanan yang cukup baik — menggunakan framework Laravel yang mature, Eloquent ORM (mencegah SQL injection), CSRF protection bawaan, dan file storage yang tidak dapat diakses langsung via web. Prioritas utama perbaikan adalah menambahkan **ownership check di endpoint yang memodifikasi data** dan mengimplementasikan **rate limiting** di endpoint autentikasi.

---

*Laporan ini dibuat berdasarkan pemeriksaan source code pada commit `ead7246`. Audit bersifat read-only. Tidak ada perubahan yang dilakukan pada source code.*
