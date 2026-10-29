# CV Elka Mandiri

Website company profile untuk CV Elka Mandiri, dibuat dengan Laravel. Pengunjung bisa melihat layanan, galeri, dan profil perusahaan, lalu mengirim pesan lewat formulir kontak. Semua isi situs diatur dari panel admin, jadi tidak perlu menyentuh kode setiap kali ada yang berubah.

## Apa saja isinya

**Halaman publik**
- Beranda, Layanan, Tentang Kami, dan Galeri
- Formulir "Kirim Pesan" di halaman Tentang Kami (dibatasi 10 pesan per jam per pengunjung supaya tidak dibanjiri spam)

**Panel admin** (`/admin`)
- Dashboard berisi ringkasan layanan, galeri, dan pesan yang belum dibaca
- Kelola layanan (termasuk draft), galeri foto, dan isi tiap halaman
- Ubah hero banner, ajakan bertindak (CTA), dan logo
- Pesan Masuk dengan notifikasi lonceng, plus catatan aktivitas admin
- Ganti profil, foto, dan password

## Yang perlu disiapkan

- PHP 8.3 atau lebih baru, dengan ekstensi GD (dukungan WebP), PDO MySQL, mbstring, dan fileinfo
- MySQL atau MariaDB yang sudah berjalan
- Composer
- Node.js dan npm

Cek dulu lewat terminal:

```bash
php -v
composer -V
node -v
```

## Cara menjalankan di komputer sendiri

1. **Ambil kodenya, lalu masuk ke foldernya.**

   ```bash
   git clone <url-repo-ini>
   cd elkamandiritest
   ```

2. **Pasang dependensi PHP dan JavaScript.**

   ```bash
   composer install
   npm install
   ```

3. **Buat file pengaturan.**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Isi akun admin di `.env`.** Cari tiga baris ini, lalu isi sesuai keinginan Anda:

   ```env
   ADMIN_NAME=AdminElka
   ADMIN_EMAIL=admin@contoh.com
   ADMIN_PASSWORD=ganti-dengan-password-kuat
   ```

   Kalau `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dikosongkan, akun admin tidak akan dibuat.

5. **Siapkan database MySQL.** Buat dulu database kosongnya, misalnya lewat terminal MySQL:

   ```sql
   CREATE DATABASE comElka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Lalu di `.env`, hapus tanda `#` di depan baris `DB_*` dan sesuaikan dengan MySQL Anda:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=comElka
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Setelah itu jalankan migrasi dan seeder. Seeder mengisi contoh layanan dan galeri, serta membuat akun admin dari langkah 4.

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

   Kalau muncul `Access denied` atau `Connection refused`, periksa lagi username, password, dan pastikan servernya MySQL menyala.

6. **Hubungkan folder gambar unggahan.** Tanpa langkah ini, gambar yang diunggah lewat admin tidak akan tampil.

   ```bash
   php artisan storage:link
   ```

7. **Bangun file CSS dan JavaScript.**

   ```bash
   npm run build
   ```

8. **Jalankan servernya.**

   ```bash
   php artisan serve
   ```

   Buka http://localhost:8000 untuk melihat situsnya, dan http://localhost:8000/admin/login untuk masuk ke panel admin dengan email dan password dari langkah 4.

Kalau Anda sedang mengubah tampilan dan ingin hasilnya langsung terlihat tanpa build ulang, jalankan `npm run dev` di terminal terpisah selagi `php artisan serve` berjalan.

## Menjalankan test

```bash
php artisan test
```

## Pengaturan yang sering diubah

Semuanya ada di `.env`:

| Pengaturan | Fungsi | Bawaan |
|---|---|---|
| `CONTACT_MAX_PER_HOUR` | Batas pesan yang boleh dikirim satu pengunjung dalam satu jam | `10` |
| `SESSION_LIFETIME` | Lama admin tetap login tanpa aktivitas (menit) | `30` |

## Sebelum dipasang di server

Beberapa hal yang gampang terlewat:

- Di `.env` server, pakai `APP_ENV=production`, `APP_DEBUG=false`, dan isi `APP_URL` dengan alamat situs Anda.
- Pasang dependensi tanpa paket pengembangan: `composer install --no-dev --optimize-autoloader`.
- Jalankan `npm run build` di server, atau unggah folder `public/build` hasil build Anda.
- Jalankan `php artisan migrate --force` dan `php artisan storage:link`.
- Jangan unggah file `public/hot`. File itu hanya ada saat `npm run dev` berjalan, dan akan membuat situs tampil tanpa gaya.
- Buat database dan pengguna MySQL khusus untuk situs ini, lalu isi `DB_*` di `.env` server. Jangan pakai akun `root`.
- Pastikan folder `storage` dan `bootstrap/cache` bisa ditulis oleh server web.
- Aktifkan HTTPS, lalu tambahkan `SESSION_SECURE_COOKIE=true` di `.env`.
