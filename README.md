# ☕ KOPI TAKALAR - POS KOTA

Aplikasi **Point of Sale (POS)** dan **Manajemen Shift Kasir** berbasis web yang dirancang khusus untuk operasional kedai kopi Kopi Takalar. Aplikasi ini dikembangkan menggunakan **Laravel 12.x** dengan fokus pada performa cepat, antarmuka responsif ramah seluler (Android & iOS), serta kemudahan pelaporan transaksi harian.

---

## 🚀 Fitur Utama

- **Dashboard POS (Point of Sale)**: Proses transaksi kasir yang intuitif dengan pencarian produk cepat.
- **Manajemen Shift Kasir**: Memulai shift dengan input stok cup awal dan menutup shift secara otomatis saat kasir keluar.
- **Pelacakan Cup Terpakai & Sisa**: Sistem cerdas yang melacak otomatis pemakaian gelas/cup berdasarkan kategori produk minuman dingin (ICE) dan panas (HOT) untuk mencegah selisih stok.
- **Rincian Pembayaran Multi-Metode**: Pembagian pelaporan otomatis berdasarkan metode pembayaran **CASH**, **QRIS**, dan **KASBON/Piutang**.
- **Struk Rekapan Kasir Mobile-Friendly**:
  - **Salin Teks (Copy)**: Salin format teks laporan untuk ditempel langsung di grup koordinasi WhatsApp.
  - **Bagikan WA (WhatsApp Share)**: Kirim draf laporan langsung ke nomor tujuan via WhatsApp Web/Mobile.
  - **Kirim Gambar**: Ekspor struk menjadi gambar beresolusi tinggi (PNG) menggunakan *html2canvas* dan bagikan langsung lewat lembar share sistem HP Android/iOS.
  - **Cetak Printer Thermal**: Teroptimasi untuk kertas thermal berukuran 58mm.
- **Panel Khusus Admin**: Manajemen data menu produk, akun kasir/karyawan, serta unduh laporan penjualan bulanan.

---

## 🛠️ Persyaratan Sistem

Sebelum melakukan instalasi, pastikan sistem Anda telah terpasang:
- **PHP** versi `8.2` atau lebih tinggi
- **Composer** (Manajer Dependensi PHP)
- **MySQL** atau MariaDB database server
- **Node.js & NPM** (untuk kompilasi aset front-end jika diperlukan)
- **Git** (Opsional, untuk clone repositori)

---

## 📖 Panduan Instalasi Lengkap

Pilih salah satu metode instalasi di bawah ini sesuai kebutuhan Anda:

### 📴 Metode 1: Instalasi Localhost Offline (Menggunakan XAMPP)

Metode ini digunakan untuk menjalankan aplikasi sebagai POS lokal di dalam kedai tanpa memerlukan koneksi internet.

1. **Unduh Proyek**:
   Clone repositori ini atau extract file ZIP proyek ke dalam folder server Anda.
   ```bash
   git clone https://github.com/username/pos-kota.git
   # Jika menggunakan XAMPP, letakkan di E:/server/POS/pos-kota atau C:/xampp/htdocs/pos-kota
   ```

2. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env` di direktori utama proyek:
   ```bash
   cp .env.example .env
   ```

3. **Install Dependensi & Generate Key**:
   Jalankan perintah berikut di terminal:
   ```bash
   composer install
   npm install
   php artisan key:generate
   ```

4. **Konfigurasi Database**:
   - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Buat database baru bernama `pos_kota`.
   - Buka file `.env` dan sesuaikan pengaturan database Anda:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=pos_kota
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Migrasi Database & Seeder**:
   Jalankan migrasi tabel beserta data awal bawaan (seperti produk default dan akun):
   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=AdminSeeder
   ```

6. **Konfigurasi Virtual Host Apache (Agar dapat diakses via `pos-kota.test`)**:
   Untuk pengalaman terbaik, konfigurasikan domain lokal Anda di Apache XAMPP:
   - Buka file `C:\Windows\System32\drivers\etc\hosts` (Jalankan notepad sebagai Administrator), tambahkan baris berikut:
     ```text
     127.0.0.1 pos-kota.test
     ```
   - Buka file konfigurasi Apache `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, tambahkan blok berikut di bagian bawah:
     ```apache
     <VirtualHost *:80>
         DocumentRoot "E:/server/POS/pos-kota/public"
         ServerName pos-kota.test
         <Directory "E:/server/POS/pos-kota/public">
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```
   - Restart Apache di XAMPP Control Panel.
   - Buka browser dan ketik `http://pos-kota.test` untuk mengakses aplikasi secara offline.

---

### 🌐 Metode 2: Localhost Online (Menggunakan Ngrok)

Metode ini sangat berguna jika Anda ingin server offline POS di kedai dapat diakses secara langsung oleh HP Android/iOS barista atau owner di luar kedai secara instan tanpa VPS/Hosting berbayar.

1. **Jalankan Server Lokal**:
   Pastikan aplikasi sudah berjalan di localhost (baik melalui Apache XAMPP port `80` atau built-in PHP server di port `8000`):
   ```bash
   # Jika menggunakan built-in PHP server
   php -S 127.0.0.1:8000 -t public
   ```

2. **Jalankan Tunneling Ngrok**:
   Buka terminal/CMD baru, jalankan perintah ngrok sesuai metode server lokal Anda:
   - **Jika menggunakan server built-in (`127.0.0.1:8000`)**:
     ```bash
     ngrok http 8000
     ```
   - **Jika menggunakan Virtual Host XAMPP (`pos-kota.test`)**:
     ```bash
     ngrok http 80 --host-header=pos-kota.test
     ```

3. **Sesuaikan Konfigurasi HTTPS di Laravel**:
   Setelah Ngrok berjalan, Anda akan mendapatkan alamat URL publik HTTPS seperti: `https://a1b2-34-56-78.ngrok-free.app`.
   - Buka file `.env` proyek Laravel Anda.
   - Cari bagian `APP_URL` dan ganti menjadi URL Ngrok tersebut:
     ```env
     APP_URL=https://a1b2-34-56-78.ngrok-free.app
     ```
   - *Catatan:* Sistem ini telah dilengkapi konfigurasi **Trust Proxies** di `bootstrap/app.php` sehingga asset CSS/JS dan skema HTTPS akan terdeteksi dengan lancar di HP Android/iOS tanpa kendala pemblokiran SSL (*Mixed Content*).

---

### ☁️ Metode 3: Instalasi di Web Server Online (Shared Hosting / cPanel)

Metode ini digunakan jika Anda ingin meletakkan aplikasi secara permanen pada domain web hosting publik (misal: `http://pos-kota.my.id`).

1. **Pemisahan Folder Proyek demi Keamanan (Sangat Direkomendasikan)**:
   Jangan mengunggah seluruh folder Laravel langsung ke dalam `public_html` karena file konfigurasi `.env` dapat diakses bebas oleh pengunjung. Lakukan pemisahan berikut:
   - Buat folder baru di direktori root hosting Anda (sejajar dengan `public_html`), misalnya beri nama `pos-core`.
   - Unggah seluruh folder proyek Laravel Anda **kecuali folder `public`** ke dalam folder `pos-core` tersebut.
   - Unggah semua isi dari folder `public` milik proyek Laravel ke dalam folder `public_html` hosting Anda.

2. **Sesuaikan Tautan `index.php`**:
   Buka file `public_html/index.php` yang baru saja diunggah, lalu sesuaikan path pemanggilan file `autoload.php` dan `app.php` ke folder `pos-core`. Ubah baris berikut:
   ```php
   // Baris 14: Ubah path bootstrap/app.php
   $app = require __DIR__.'/../pos-core/bootstrap/app.php';
   ```
   *(Catatan: Sesuaikan `__DIR__.'/../pos-core/...` tergantung struktur peletakan folder Anda).*

3. **Buat Database di cPanel**:
   - Masuk ke cPanel Anda, pilih **MySQL Database Wizard**.
   - Buat nama database baru, user database baru, dan berikan semua hak akses (*All Privileges*).

4. **Konfigurasi File `.env`**:
   - Buat file `.env` di dalam folder `pos-core/`.
   - Ubah parameter database dan URL domain Anda sesuai data hosting baru:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://domain-anda.com

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_DATABASE=nama_db_hosting
     DB_USERNAME=user_db_hosting
     DB_PASSWORD=password_db_hosting
     ```

5. **Migrasi Database & Seeder**:
   - Jika hosting Anda mendukung akses SSH, masuk ke terminal SSH dan jalankan perintah di folder `pos-core`:
     ```bash
     php artisan migrate --seed
     php artisan db:seed --class=AdminSeeder
     ```
   - Jika hosting tidak memiliki fitur SSH, Anda dapat melakukan migrasi dengan membuat route sementara di file `routes/web.php` hosting Anda:
     ```php
     Route::get('/run-migration-seed', function() {
         Artisan::call('migrate:fresh --seed');
         Artisan::call('db:seed --class=AdminSeeder');
         return "Database migrated & seeded successfully!";
     });
     ```
     Akses URL `https://domain-anda.com/run-migration-seed` sekali saja di browser, lalu hapus kembali route tersebut demi keamanan.

---

## 🔑 Akun Pengujian Default

Gunakan akun berikut setelah database berhasil di-seed untuk masuk ke sistem:

### Akun Administrator
- **Email**: `admin@kopitakalar.com`
- **Password**: `master123`
- **Nama**: Fadlika Rahman

---

## 📦 Menambahkan Perubahan ke GitHub
Setelah memodifikasi dokumentasi ini, jalankan perintah berikut di terminal Anda untuk memperbarui repositori GitHub:
```bash
git add README.md
git commit -m "Update README.md dengan dokumentasi lengkap instalasi offline & online"
git push
```

---

*Dikembangkan untuk kelancaran bisnis Kopi Takalar. Hak Cipta &copy; 2026. Lisensi MIT.*
