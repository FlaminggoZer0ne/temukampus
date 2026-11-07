# Deploy TemuKampus ke Railway

Panduan dari repo GitHub sampai aplikasi bisa dibuka lewat link `https://...up.railway.app`. Docker tidak diperlukan.

> **Status panduan.** Disusun dari dokumentasi Railway/Railpack (dibaca November 2026). Kode aplikasi sudah diuji lewat simulasi build production di lokal (`composer install --no-dev`, `artisan optimize`, MySQL, header proxy HTTPS). Langkah di dashboard Railway **belum dijalankan di akun Railway sungguhan**, jadi nama menu bisa sedikit berbeda. Bila ada beda, ikuti pesan di tab **Deploy Logs**.

## Yang sudah disiapkan di kode

| Hal | Keterangan |
|---|---|
| HTTPS di belakang proxy | `trustProxies`, URL dipaksa `https://` di production, cookie sesi `Secure` |
| URL foto | Mengikuti host request, tidak bergantung `APP_URL` |
| Seeder aman diulang | Dilewati bila data demo sudah ada |
| Password akun demo | Bisa diatur lewat variable `SEED_PASSWORD` |
| Tanpa Node/Vite | Tidak ada `package.json`, jadi builder tidak menjalankan `npm` |

Railway memakai **Railpack**: mendeteksi Laravel dari file `artisan`, memasang dependency Composer, men-cache config/route/view saat build, lalu saat start menjalankan **migrasi**, **`storage:link`**, dan optimize, dan menyajikan aplikasi lewat FrankenPHP dengan document root `public/`.

## Langkah 1: Pastikan kode ada di GitHub

Repo: `https://github.com/FlaminggoZer0ne/temukampus` (branch `main`). Setiap `git push` ke branch ini akan memicu deploy otomatis.

## Langkah 2: Buat project dan database MySQL

1. Buka https://railway.com dan login (bisa dengan akun GitHub).
2. **New Project** → **Provision MySQL** (atau **+ New** → **Database** → **Add MySQL**).
3. Tunggu sampai service MySQL berstatus aktif. Catat **nama service**-nya (biasanya `MySQL`), karena dipakai di variable referensi pada langkah 4.

## Langkah 3: Tambah service aplikasi dari GitHub

1. Di project yang sama: **+ New** → **GitHub Repo** → pilih `temukampus`. Izinkan Railway mengakses repo bila diminta.
2. Jangan tunggu deploy pertama selesai. Lanjut ke langkah 4 untuk mengisi variable. Bila deploy pertama sempat gagal karena variable belum ada, itu normal.

## Langkah 4: Isi Variables

Buka service aplikasi → tab **Variables** → **Raw Editor**, tempel (ganti nilai yang bertanda `<...>`):

```env
APP_NAME=TemuKampus
APP_ENV=production
APP_DEBUG=false
APP_KEY=<hasil perintah di bawah>
APP_LOCALE=id
LOG_CHANNEL=stderr

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database

RAILPACK_PHP_EXTENSIONS=pdo_mysql
SEED_PASSWORD=<password untuk semua akun demo, jangan "password">
```

Membuat `APP_KEY` di komputer Anda:

```powershell
cd "D:\KULIAHDATA\semester 5\Pemrograman WEB\temukampus-app"
C:\xampp\php\php.exe artisan key:generate --show
```

Salin hasilnya (berawalan `base64:`) ke `APP_KEY`.

Catatan:
- Bila nama service database Anda bukan `MySQL`, ganti bagian `MySQL` pada `${{MySQL.…}}`. Anda juga bisa memilih variable dari tombol penyisipan referensi di editor Railway.
- `APP_URL` diisi di langkah 6, setelah domain ada.
- Config di-cache saat build. Setelah mengubah variable, Railway akan membangun ulang, jadi tunggu deploy baru selesai.

## Langkah 5: Pasang Volume untuk foto upload

Disk Railway sifatnya sementara. Tanpa volume, foto barang **hilang setiap redeploy**.

1. Di canvas project: klik kanan area kosong (atau tekan `Ctrl+K`) → **Create Volume**.
2. Pilih service aplikasi sebagai tujuan.
3. **Mount path:** `/app/storage/app/public`

Catatan dari dokumentasi Railway:
- Volume dipasang saat container *start*, bukan saat build, jadi cocok dengan `storage:link` yang dijalankan saat start.
- Volume dipasang sebagai `root`. Bila upload foto gagal dengan error izin akses (*permission denied*), tambahkan variable `RAILWAY_RUN_UID=0` pada service aplikasi.

## Langkah 6: Buka akses publik dan isi APP_URL

1. Service aplikasi → **Settings** → **Networking** → **Generate Domain**.
2. Salin domainnya, misalnya `https://temukampus-production.up.railway.app`.
3. Kembali ke **Variables**, tambahkan:
   ```env
   APP_URL=https://temukampus-production.up.railway.app
   ```
4. Tunggu deploy ulang selesai.

(Opsional) **Settings** → **Deploy** → **Healthcheck Path**: `/up`.

## Langkah 7: Isi data awal (sekali saja)

Migrasi berjalan otomatis saat start. Data awal (akun demo, kategori, lokasi) perlu di-seed satu kali lewat **Pre-Deploy Command**:

1. Service aplikasi → **Settings** → **Deploy** → **Pre-Deploy Command**:
   ```
   php artisan migrate --force && php artisan db:seed --force
   ```
2. Deploy ulang (**Deploy** / push commit kecil).
3. Setelah berhasil, **kosongkan** kolom Pre-Deploy Command itu.

Seeder aman bila terlanjur dibiarkan: ia dilewati kalau data demo sudah ada. Pre-Deploy berjalan sebelum volume dipasang, tetapi seeder hanya menyentuh database, jadi tidak masalah.

## Langkah 8: Uji

Buka domain Anda lalu cek:

1. Halaman beranda tampil lengkap (CSS, logo, ikon). Gembok HTTPS aktif.
2. Login `admin@temukampus.test` dengan password dari `SEED_PASSWORD`.
3. Sebagai mahasiswa, buat laporan dengan foto, lalu buka detailnya. Foto harus tampil.
4. Lakukan **redeploy**, lalu cek foto itu masih ada (bukti volume bekerja).

Setelah selesai, **ganti password akun demo** lewat menu profil, atau hapus akun yang tidak dipakai di halaman Kelola User.

## Update aplikasi

```bash
git add -A && git commit -m "pesan" && git push
```

Railway membangun ulang otomatis dan menjalankan migrasi baru saat start.

## Masalah umum

| Gejala | Penyebab dan solusi |
|---|---|
| Halaman error 500 | Buka **Deploy Logs**. Paling sering `APP_KEY` kosong atau salah format (harus berawalan `base64:`). |
| `could not find driver` | Ekstensi MySQL belum aktif. Pastikan `RAILPACK_PHP_EXTENSIONS=pdo_mysql` ada, lalu deploy ulang. |
| `Connection refused ... 127.0.0.1` | Variable `DB_*` tidak ter-resolve. Cek nama service di `${{MySQL.…}}` dan pastikan service MySQL aktif. |
| Login berhasil tetapi kembali ke halaman login | Buka lewat `https://` domain Railway, bukan `http://`. Cookie sesi di production hanya dikirim lewat HTTPS. |
| CSS atau ikon tidak muncul | Pastikan `APP_ENV=production`, lalu deploy ulang. |
| Foto hilang setelah redeploy | Volume belum terpasang atau mount path salah. Harus persis `/app/storage/app/public`. |
| Upload foto gagal, *permission denied* | Tambah variable `RAILWAY_RUN_UID=0`. |
| Foto tampil sebagai gambar rusak | Cek di Deploy Logs bahwa `storage:link` berjalan saat start, dan volume memakai mount path yang benar. |
| Perubahan variable tidak berefek | Config di-cache saat build. Tunggu deploy ulang selesai. |

## Catatan keamanan

- Jangan commit file `.env`. File itu sudah masuk `.gitignore`.
- Selalu set `APP_DEBUG=false` di production.
- Akun demo bersifat publik selama situs aktif. Ganti passwordnya atau hapus akunnya.
