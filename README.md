# SIPEKA — Sistem Informasi Pengajuan Kartu Mahasiswa
### STMIK Adhi Guna

SIPEKA adalah aplikasi berbasis web yang digunakan untuk mengelola permohonan, verifikasi, serta penerbitan Kartu Tanda Mahasiswa (KTM) digital secara online dan terintegrasi. Dilengkapi dengan fitur pemotongan pas foto langsung, QR code verifikasi keaslian KTM, serta notifikasi email otomatis saat status pengajuan diverifikasi oleh pihak akademik.

---

## 📌 Prasyarat Sistem

Sebelum melakukan instalasi, pastikan sistem/komputer Anda telah terpasang:

- **PHP** `>= 8.3` (pastikan ekstensi `pdo_mysql`, `gd`, `fileinfo`, `mbstring`, `openssl`, `curl` aktif)
- **Composer** `>= 2.0`
- **Node.js** `>= 18.x` & **NPM**
- **Database Server** (MySQL atau MariaDB)

---

## 🚀 Panduan Instalasi & Setup

Ikuti langkah-langkah di bawah ini untuk memasang project di lingkungan lokal:

### 1. Clone Repository & Masuk ke Folder Project
```bash
git clone https://github.com/Fardiansyah-Project/SI-Peka.git
cd SI-Peka
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
```bash
npm install
```

### 4. Salin & Konfigurasi File Environment
Salin file `.env.example` menjadi `.env`:

**Windows (PowerShell):**
```powershell
copy .env.example .env
```

**Linux / macOS:**
```bash
cp .env.example .env
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Konfigurasi Database & Email di `.env`
Buka file `.env` dan sesuaikan pengaturan koneksi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sipandu
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan:** Pastikan database (contoh: `db_sipandu`) sudah dibuat terlebih dahulu di MySQL/phpMyAdmin Anda.

Untuk pengaturan notifikasi email:
```env
# Mode pengembangan (email dicatat di storage/logs/laravel.log):
MAIL_MAILER=log

# Mode produksi / SMTP langsung (misal: Gmail SMTP):
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=email_anda@gmail.com
# MAIL_PASSWORD=app_password_anda
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS="noreply@stmik-adhiguna.ac.id"
# MAIL_FROM_NAME="${APP_NAME}"
```

### 7. Jalankan Migrasi & Seeder Database
Jalankan migrasi tabel beserta akun admin awal:
```bash
php artisan migrate --seed
```

### 8. Hubungkan Storage Publik
PENTING: Buat symlink storage agar pas foto mahasiswa dapat diakses dan diunduh:
```bash
php artisan storage:link
```

### 9. Build Aset Frontend
Lakukan kompilasi aset CSS/JS dengan Vite:

**Untuk produksi:**
```bash
npm run build
```

**Atau untuk pengembangan (Hot Module Replacement):**
```bash
npm run dev
```

---

## 🖥️ Menjalankan Aplikasi

Jalankan local development server Laravel:
```bash
php artisan serve
```

Akses aplikasi melalui browser di:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## ⚙️ Konfigurasi Tambahan untuk Production (Unduh Kartu Depan)

Fitur unduh kartu bagian depan menggunakan **Headless Chromium / Chrome** untuk merender template HTML menjadi gambar PNG (`--screenshot`).

Jika di-deploy ke server Linux (VPS / Ubuntu / Debian / Docker):
1. **Wajib install Chromium di server:**
   ```bash
   sudo apt update
   sudo apt install -y chromium-browser # atau: sudo apt install -y chromium
   ```
2. **Atur lokasi binary di file `.env` server jika tidak terdeteksi otomatis:**
   ```env
   KTM_CHROME_PATH=/usr/bin/chromium-browser
   # atau
   # KTM_CHROME_PATH=/usr/bin/chromium
   ```
3. **Pastikan aset frontend telah di-compile:**
   ```bash
   npm run build
   ```

---

## 🔑 Kredensial Akun Default (Admin)

Setelah menjalankan `php artisan migrate --seed`, akun administrator default yang dapat digunakan adalah:

| Informasi | Keterangan |
| :--- | :--- |
| **URL Login** | `http://localhost:8000/login` |
| **Email** | `adminganteng@gmail.com` |
| **Password** | `password` |

---

## ✨ Fitur Utama

1. **Formulir Pengajuan Mandiri:** Mahasiswa menginput Nama, NIM, Email, Angkatan, Program Studi, dan pas foto formal.
2. **Interactive Photo Cropper:** Pratinjau pas foto dengan rasio 3:4 langsung di browser sebelum diunggah.
3. **Notifikasi Email Real-Time:** Email otomatis terkirim ke mahasiswa saat pengajuan disetujui atau ditolak (beserta catatan revisi).
4. **Verifikasi Publik & QR Code:** Setiap KTM yang disetujui memiliki QR Code yang mengarah ke halaman verifikasi resmi.
5. **Dashboard Admin:**
   - Rekap statistik pengajuan (Total, Menunggu, Disetujui, Ditolak).
   - Fitur pencarian nama/NIM dan filter status.
   - Setujui / tolak pengajuan dengan catatan alasan penolakan.
   - Pratinjau dan unduh kartu KTM bagian depan & belakang.
