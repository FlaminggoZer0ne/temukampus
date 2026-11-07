# Panduan Setup TemuKampus

Folder ini berisi **lapisan aplikasi** (model, controller, migration, view, CSS/JS) yang mengikuti
`TemuKampus-README.md` dan desain Figma. Kerangka Laravel-nya (`vendor/`, `artisan`, `config/`, dst.)
dibuat dengan Composer lalu digabung dengan folder ini.

## 1. Kebutuhan

- PHP **≥ 8.1** (XAMPP Anda saat ini PHP 8.0 → upgrade, atau pakai Laragon)
- Composer
- MySQL / MariaDB

## 2. Gabungkan dengan kerangka Laravel

```powershell
# Dari folder induk Tubes_TemuKampus
composer create-project laravel/laravel temukampus-app "^11.0"   # atau ^10.0

# Salin lapisan aplikasi ke atas kerangka (timpa file yang sama)
robocopy Tubes_TemuKampus temukampus-app /E /XF TemuKampus-README.md PANDUAN-SETUP.md
```

Hapus migration bawaan Laravel yang bentrok dengan tabel `users` kita:

```powershell
cd temukampus-app
# Laravel 11:
del database\migrations\0001_01_01_000000_create_users_table.php
# Laravel 10:
del database\migrations\2014_10_12_000000_create_users_table.php   # (sudah tertimpa), hapus juga 2014_10_12_100000_create_password_reset_tokens_table.php dan *_create_password_resets_table.php
```

> Migration `users` milik kita sudah memuat tabel `password_reset_tokens` dan `sessions`.

## 3. Konfigurasi `.env`

```env
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta     # Laravel 11. Di Laravel 10 ubah 'timezone' di config/app.php

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=temukampus
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `temukampus` di phpMyAdmin.

## 4. Dua baris di `app/Providers/AppServiceProvider.php`

```php
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

public function boot(): void
{
    Paginator::useBootstrapFive();
    Carbon::setLocale('id');
}
```

## 5. Jalankan

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka http://127.0.0.1:8000. Akun demo ada di README bagian 14
(`admin@`, `petugas@`, `mahasiswa@temukampus.test`, password `password`).

## Catatan implementasi

- Middleware role dipakai lewat nama kelas (`RoleMiddleware::class.':admin'`) sehingga **tidak perlu
  mendaftarkan alias** di `Kernel.php` / `bootstrap/app.php`.
- Tabel `kategori` dan `lokasi` mendapat kolom tambahan `keterangan` (nullable) karena ada di desain Figma.
- Grafik dashboard memakai batang CSS yang digambar jQuery dari `/api/statistik`, mengikuti desain Figma
  (bukan Chart.js).
- Ikon adalah SVG asli dari Figma di `public/img/icons`, diwarnai lewat CSS mask.
- Font (Rubik, Nunito Sans), Bootstrap 5, jQuery dimuat dari CDN, jadi butuh internet saat dijalankan.
