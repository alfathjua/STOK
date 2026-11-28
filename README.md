# JaStock - Sistem Manajemen Stok CV. Jada Team Sedaya

JaStock adalah aplikasi web berbasis Laravel untuk mengelola stok barang masuk dan keluar di CV. Jada Team Sedaya. Aplikasi ini dirancang dengan dua peran utama: **Director** dan **Foreman (Mandor)**.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Struktur Database](#struktur-database)
- [Struktur Direktori](#struktur-direktori)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

---

## 🚀 Fitur Utama

### Role: Director
- **Dashboard**: Ringkasan total stok masuk, stok keluar, dan stok tersedia
- **Stok Masuk**: Kelola data barang masuk dengan upload bukti (struk/foto)
- **Stok Gudang**: Pantau sisa stok real-time per item
- **Stok Keluar**: Catat barang keluar dengan informasi penerima
- **Riwayat Stok Keluar**: Lihat histori lengkap transaksi keluar
- **Track Aktivitas**: Monitor semua aktivitas create/edit/delete di sistem
- **Manajemen Mandor**: Generate kunci registrasi untuk mandor baru
- **Profile Management**: Update info pribadi, foto profil, dan password

### Role: Foreman (Mandor)
- **Dashboard**: Lihat ringkasan stok
- **Stok Masuk**: Input barang masuk dengan bukti
- **Stok Gudang**: Lihat sisa stok
- **Stok Keluar**: Catat barang keluar
- **Riwayat Stok Keluar**: Lihat histori transaksi keluar
- **Profile Management**: Update info dan password

### Fitur Keamanan
- Registrasi mandor menggunakan kunci 6 karakter (valid 24 jam)
- Rate limiting untuk login dan registrasi
- Password hashing dengan bcrypt
- CSRF protection
- Private file storage untuk dokumen sensitif
- Security headers middleware

---

## 🛠️ Teknologi

- **Framework**: Laravel 13.17
- **PHP**: 8.3+
- **Frontend**: Tailwind CSS 4 via Vite
- **Database**: MySQL 8.0
- **Session**: Database-driven
- **Cache**: Database-driven
- **Queue**: Database-driven

---

## 🗄️ Struktur Database

### Tabel Utama

#### 1. **users**
Menyimpan data pengguna (director dan foreman).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| name | varchar | Nama lengkap |
| username | varchar | Username unik untuk login |
| email | varchar | Email (unique) |
| password | varchar | Password (hashed) |
| phone | varchar | Nomor WhatsApp (opsional) |
| avatar | varchar | Path foto profil di storage |
| role | enum('director','foreman') | Peran user |
| email_verified_at | timestamp | Verifikasi email |
| remember_token | varchar | Remember me token |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 2. **stock_ins**
Menyimpan header transaksi stok masuk.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| uuid | varchar | UUID untuk URL friendly |
| user_id | bigint | Foreign key ke users |
| stock_origin | varchar | Asal stok (nama toko/supplier) |
| proof | varchar | Path file bukti (nullable) |
| created_at | timestamp | Tanggal pencatatan |
| updated_at | timestamp | |

#### 3. **detail_stocks**
Menyimpan detail barang per baris stok masuk.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| stock_in_id | bigint | Foreign key ke stock_ins |
| item_name | varchar | Nama barang |
| description | text | Deskripsi barang |
| qty | integer | Jumlah barang masuk |
| price | decimal(12,2) | Harga satuan |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 4. **receipts**
Menyimpan multiple foto struk per transaksi stok masuk.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| stock_in_id | bigint | Foreign key ke stock_ins |
| file_path | varchar | Path file di storage |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 5. **stocks**
Menyimpan sisa stok gudang per item (snapshot harian).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| detail_stock_id | bigint | Foreign key ke detail_stocks |
| date | date | Tanggal snapshot |
| qty_in | integer | Jumlah masuk (mirror dari detail) |
| qty_remaining | integer | Sisa stok (editable) |
| created_at | timestamp | |
| updated_at | timestamp | |

**Constraint**: `unique(['detail_stock_id', 'date'])` - satu item hanya boleh punya satu record per tanggal.

#### 6. **stock_outs**
Menyimpan transaksi barang keluar.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| stock_id | bigint | Foreign key ke stocks |
| detail_stock_id | bigint | Foreign key ke detail_stocks |
| user_id | bigint | Foreign key ke users (pencatat) |
| company_name | varchar | Nama penerima barang |
| map_url | varchar | Link Google Maps lokasi penerima (nullable) |
| qty | integer | Jumlah barang keluar |
| price | decimal(12,2) | Harga satuan snapshot |
| created_at | timestamp | Tanggal transaksi |
| updated_at | timestamp | |

#### 7. **activity_logs**
Menyimpan log aktivitas user di sistem.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke users (nullable) |
| action | enum('create','edit','delete') | Jenis aksi |
| feature | varchar | Nama fitur (e.g., "Stok Masuk") |
| description | text | Deskripsi singkat aksi |
| created_at | timestamp | |
| updated_at | timestamp | |

#### 8. **foreman_keys**
Menyimpan kunci registrasi untuk mandor.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| key | varchar(6) | Kode unik 6 karakter (huruf+angka) |
| is_used | boolean | Status penggunaan (default: false) |
| used_by | bigint | Foreign key ke users (nullable) |
| created_at | timestamp | Waktu pembuatan (expired +24 jam) |
| updated_at | timestamp | |

---

## 📁 Struktur Direktori

```
STOK/
├── app/
│   ├── Http/
│   │   ├── Controllers/           # Controllers untuk routing
│   │   │   ├── AuthController.php        # Login/logout
│   │   │   ├── DashboardController.php   # Dashboard
│   │   │   ├── ForemanController.php     # Manajemen mandor (director only)
│   │   │   ├── ProfileController.php     # Profile management
│   │   │   ├── RegisterController.php    # Registrasi mandor
│   │   │   ├── StockController.php       # Stok gudang
│   │   │   ├── StockInController.php     # Stok masuk
│   │   │   ├── StockOutController.php    # Stok keluar
│   │   │   └── TrackController.php       # Activity tracking (director only)
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php        # Authorization based on role
│   │       └── SecurityHeaders.php       # Security headers
│   ├── Models/                    # Eloquent models
│   │   ├── ActivityLog.php
│   │   ├── DetailStock.php
│   │   ├── ForemanKey.php
│   │   ├── Receipt.php
│   │   ├── Stock.php
│   │   ├── StockIn.php
│   │   ├── StockOut.php
│   │   └── User.php
│   └── Services/
│       └── ActivityLogger.php     # Service untuk logging aktivitas
│
├── database/
│   ├── migrations/                # Database migrations
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   └── UserSeeder.php         # Seeder default director & foreman
│   └── factories/
│       └── UserFactory.php
│
├── resources/
│   ├── css/
│   │   └── app.css                # Tailwind CSS entry point
│   ├── js/
│   │   └── app.js                 # JavaScript entry point
│   └── views/
│       ├── auth/                  # Login & register views
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dash/                  # Dashboard views
│       │   ├── layouts/
│       │   │   ├── app.blade.php       # Layout utama dengan sidebar
│       │   │   └── grid.blade.php      # Layout grid untuk cards
│       │   ├── foreman/
│       │   │   └── index.blade.php     # Manajemen mandor
│       │   ├── profile/
│       │   │   └── user.blade.php      # Profile page
│       │   ├── stock/
│       │   │   ├── index.blade.php
│       │   │   └── partials/list.blade.php
│       │   ├── stock-in/
│       │   │   ├── index.blade.php
│       │   │   ├── action.blade.php    # Create/edit form
│       │   │   └── partials/list.blade.php
│       │   ├── stock-out/
│       │   │   ├── index.blade.php
│       │   │   ├── history.blade.php
│       │   │   └── partials/
│       │   │       ├── list.blade.php
│       │   │       ├── history-list.blade.php
│       │   │       └── modal-out.blade.php
│       │   ├── track/
│       │   │   ├── index.blade.php
│       │   │   └── partials/list.blade.php
│       │   └── index.blade.php         # Dashboard homepage
│       └── index.blade.php             # Landing page
│
├── routes/
│   └── web.php                    # Web routes
│
├── storage/
│   └── app/
│       ├── private/
│       │   ├── receipts/          # Upload struk stok masuk
│       │   ├── fp-director/       # Avatar director
│       │   └── fp-foreman/        # Avatar foreman
│       └── public/
│
├── public/
│   ├── build/                     # Compiled Vite assets (after npm run build)
│   ├── logo.webp                  # Logo aplikasi
│   └── favicon.webp               # Favicon
│
├── .env.example                   # Environment template
├── composer.json                  # PHP dependencies
├── package.json                   # Node.js dependencies
├── vite.config.js                 # Vite configuration
└── tailwind.config.js             # Tailwind configuration
```

---

## 📦 Instalasi

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & npm
- MySQL 8.0+
- Git

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd STOK
   ```

2. **Install dependencies PHP**
   ```bash
   composer install
   ```

3. **Install dependencies Node.js**
   ```bash
   npm install
   ```

4. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Konfigurasi database**
   
   Buka file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=stok_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Buat database**
   ```bash
   mysql -u root -p
   CREATE DATABASE stok_db;
   exit;
   ```

8. **Jalankan migrasi & seeder**
   ```bash
   php artisan migrate --seed
   ```

9. **Build assets frontend**
   ```bash
   npm run build
   ```

10. **Set permission storage & cache**
    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

11. **Buat symlink storage (jika diperlukan)**
    ```bash
    php artisan storage:link
    ```

---

## ⚙️ Konfigurasi

### Environment Variables (.env)

#### Production Settings
```env
APP_NAME=JaStock
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### Session & Cache
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### Security
```env
BCRYPT_ROUNDS=12
```

### Default User Accounts (Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun default berikut tersedia:

| Role | Email | Username | Password |
|------|-------|----------|----------|
| Director | director@jastock.id | director | password |
| Foreman | foreman@jastock.id | foreman | password |

**⚠️ PENTING**: Ubah password default segera setelah login pertama kali!

---

## 📖 Penggunaan

### 1. Menjalankan Aplikasi (Development)

**Opsi A: Menggunakan Laravel Artisan**
```bash
php artisan serve
```
Akses di `http://localhost:8000`

**Opsi B: Menggunakan Vite Dev Server (Hot Reload)**

Terminal 1 - Laravel:
```bash
php artisan serve
```

Terminal 2 - Vite:
```bash
npm run dev
```

### 2. Login

Akses halaman login di `/login` dan masukkan kredensial:
- **Director**: `director` / `password`
- **Foreman**: `foreman` / `password`

### 3. Registrasi Mandor Baru

#### Sebagai Director:
1. Login sebagai director
2. Buka menu **Data Mandor** (`/foreman`)
3. Klik **Generate Kunci Baru**
4. Salin kunci 6 karakter yang dihasilkan
5. Bagikan kunci ke calon mandor (valid 24 jam)

#### Sebagai Calon Mandor:
1. Akses halaman registrasi `/register`
2. Masukkan:
   - Nama lengkap
   - Email
   - Username (unik)
   - Password (min. 8 karakter)
   - Kunci registrasi dari director
3. Klik **Daftar**

### 4. Workflow Stok Masuk

1. Login sebagai director atau foreman
2. Buka menu **Stok Masuk** (`/stock-in`)
3. Klik **Tambah Stok Masuk**
4. Isi form:
   - **Asal Stok**: nama supplier/toko
   - **Upload Struk**: upload foto struk (opsional, bisa multiple)
   - **Tambah Baris**: klik untuk menambah item
   - Per baris isi:
     - Nama barang
     - Deskripsi
     - Jumlah (qty)
     - Harga satuan
5. Klik **Simpan**

### 5. Memantau Stok Gudang

1. Buka menu **Stok** (`/stock`)
2. Lihat list barang dengan kolom:
   - Nama barang
   - Deskripsi
   - Jumlah masuk
   - Sisa stok
   - Tanggal
3. Gunakan search untuk filter cepat

### 6. Mencatat Stok Keluar

1. Buka menu **Stok Keluar** (`/stock-out`)
2. Klik **Catat Stok Keluar** pada item yang ingin dicatat
3. Isi modal form:
   - **Nama Penerima**: nama perusahaan/orang
   - **Link Google Maps**: URL lokasi (opsional)
   - **Jumlah Keluar**: qty yang diambil
4. Klik **Catat Keluar**
5. Sistem otomatis mengurangi `qty_remaining` di stok gudang

### 7. Melihat Riwayat Stok Keluar

1. Buka menu **Riwayat Keluar** (`/stock-out/history`)
2. Lihat tabel lengkap dengan:
   - Nama barang
   - Penerima
   - Jumlah keluar
   - Tanggal transaksi
   - Link Google Maps (jika ada)
3. Gunakan search untuk filter

### 8. Tracking Aktivitas (Director Only)

1. Login sebagai director
2. Buka menu **Track** (`/track`)
3. Lihat log aktivitas dengan:
   - Nama user
   - Aksi (create/edit/delete)
   - Fitur
   - Deskripsi
   - Waktu
4. Gunakan search untuk filter

### 9. Update Profile

1. Klik nama user di navbar kanan atas
2. Pilih **Profile**
3. Tab **Informasi Akun**:
   - Update nama, username, email, nomor WA
   - Upload foto profil
4. Tab **Keamanan**:
   - Ubah password
   - Input password lama dan password baru
5. Klik **Simpan**

---

## 🚀 Deployment

### Checklist Sebelum Deploy

#### 1. **Environment Production**
Edit `.env` di server:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### 2. **Build Assets**
Jalankan di lokal, lalu upload folder `public/build/`:
```bash
npm run build
```

Atau jalankan di server (jika Node.js tersedia):
```bash
npm install
npm run build
```

#### 3. **Optimize Laravel**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### 4. **Set Permission**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 5. **Migrasi Database**
```bash
php artisan migrate --force --seed
```

### Deployment ke Shared Hosting (cPanel)

1. **Upload files via FTP/File Manager**
   - Upload semua file kecuali `node_modules/` dan `.git/`
   - Pastikan `public/build/` sudah ada

2. **Setup `.env`**
   - Rename `.env.example` menjadi `.env`
   - Edit konfigurasi database sesuai cPanel

3. **Set document root**
   - Arahkan document root domain ke folder `public/`

4. **Install Composer dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Generate key**
   ```bash
   php artisan key:generate
   ```

6. **Clear cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

### Deployment ke VPS (Linux)

Contoh menggunakan Ubuntu + Nginx:

1. **Install PHP 8.3 & Extensions**
   ```bash
   sudo apt update
   sudo apt install php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl
   ```

2. **Install Composer**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

3. **Clone & Install**
   ```bash
   cd /var/www/
   git clone <repo-url> jastock
   cd jastock
   composer install --no-dev --optimize-autoloader
   ```

4. **Setup Nginx**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/jastock/public;

       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

5. **Set Permission**
   ```bash
   sudo chown -R www-data:www-data /var/www/jastock
   sudo chmod -R 775 storage bootstrap/cache
   ```

6. **Enable site & restart Nginx**
   ```bash
   sudo ln -s /etc/nginx/sites-available/jastock /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

---

## 🛠️ Troubleshooting

### Error 500 Saat Deploy

**Penyebab umum:**

1. **File `public/build/manifest.json` tidak ada**
   
   **Solusi**: Jalankan `npm run build` di lokal, lalu upload folder `public/build/` ke server.

2. **Permission folder storage salah**
   
   **Solusi**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

3. **Migration belum dijalankan**
   
   **Solusi**:
   ```bash
   php artisan migrate --force
   ```

4. **Cache config lama**
   
   **Solusi**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

5. **APP_KEY tidak di-set**
   
   **Solusi**:
   ```bash
   php artisan key:generate
   ```

### CSS Tailwind Tidak Muncul

**Penyebab**: Assets Vite belum di-build.

**Solusi**:
```bash
npm run build
```

Pastikan folder `public/build/` ada dan berisi:
- `manifest.json`
- `assets/*.css`
- `assets/*.js`

### Gagal Upload File

**Penyebab**: Permission folder `storage/app/private/` tidak benar.

**Solusi**:
```bash
chmod -R 775 storage/app
```

### Session Tidak Persist

**Penyebab**: Tabel `sessions` belum dibuat.

**Solusi**:
```bash
php artisan migrate
```

Pastikan di `.env`:
```env
SESSION_DRIVER=database
```

### Kunci Registrasi Expired

**Behavior**: Kunci foreman expired setelah 24 jam dari `created_at`.

**Solusi**: Director generate kunci baru dari menu **Data Mandor**.

---

## 📄 License

Proprietary - CV. Jada Team Sedaya

---

## 👥 Support

Untuk pertanyaan atau bantuan teknis, hubungi tim development.
