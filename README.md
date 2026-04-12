# Geeko Komputer — Aplikasi Estimasi & Manajemen Servis

Aplikasi web berbasis PHP + MySQL untuk mengelola estimasi biaya servis komputer secara transparan. Pelanggan dapat mengisi form estimasi, melakukan booking servis, dan memantau status perbaikan secara real-time. Teknisi dapat mengelola dan mengupdate status servis dari dashboard khusus.

---

## Teknologi yang Digunakan

- **Frontend**: HTML, Tailwind CSS (compiled via CLI),
- **Backend**: PHP (Native, PDO)
- **Database**: MySQL
- **Server**: XAMPP (Apache + MySQL)
- **Icon**: Font Awesome 6

---

## Fitur Utama

- Estimasi biaya otomatis berdasarkan jenis perangkat dan kerusakan
- Popup hasil estimasi dengan pilihan booking langsung
- Sistem autentikasi (login, register, logout) berbasis session
- Dashboard pelanggan — lihat daftar booking dan status servis
- Dashboard teknisi — kelola servis, update status, tambah catatan
- Riwayat update status dalam bentuk timeline
- Filter status servis
- Responsif (mobile & desktop)

---

## Struktur Database

Database: `cost_db`

### Tabel `users`
Menyimpan akun pelanggan dan teknisi.

| Kolom       | Tipe                        | Keterangan                    |
|-------------|-----------------------------|-------------------------------|
| id          | INT UNSIGNED AUTO_INCREMENT | Primary key                   |
| nama        | VARCHAR(100)                | Nama lengkap                  |
| email       | VARCHAR(150) UNIQUE         | Email login                   |
| no_telepon  | VARCHAR(20)                 | Nomor telepon                 |
| password    | VARCHAR(255)                | Password (bcrypt hash)        |
| role        | ENUM('pelanggan','teknisi') | Peran user                    |
| created_at  | TIMESTAMP                   | Waktu registrasi              |

---

### Tabel `servis`
Menyimpan data booking servis dari pelanggan.

| Kolom           | Tipe                                                         | Keterangan                        |
|-----------------|--------------------------------------------------------------|-----------------------------------|
| id              | INT UNSIGNED AUTO_INCREMENT                                  | Primary key                       |
| nomor_tiket     | VARCHAR(20) UNIQUE                                           | Kode tiket (format: GK-YYYYMMDD-XXXX) |
| user_id         | INT UNSIGNED (FK → users.id)                                 | Pemilik booking                   |
| nama_pelanggan  | VARCHAR(100)                                                 | Nama pelanggan                    |
| email           | VARCHAR(150)                                                 | Email pelanggan                   |
| no_telepon      | VARCHAR(20)                                                  | Nomor telepon                     |
| perangkat       | ENUM('macbook','windows','pc','imac','other')                | Jenis perangkat                   |
| jenis_kerusakan | ENUM('lcd','battery','ssd','thermal','other')                | Jenis kerusakan                   |
| cabang          | ENUM('surabaya')                                             | Cabang servis                     |
| deskripsi       | TEXT                                                         | Deskripsi keluhan                 |
| estimasi_harga  | DECIMAL(12,2)                                                | Estimasi biaya (dari tabel harga) |
| foto            | VARCHAR(255)                                                 | Path foto kerusakan (opsional)    |
| status          | ENUM('Diterima','Sedang dicek','Perbaikan','Testing','Selesai') | Status terkini                 |
| created_at      | TIMESTAMP                                                    | Waktu booking masuk               |

---

### Tabel `servis_log`
Menyimpan riwayat setiap perubahan status oleh teknisi.

| Kolom      | Tipe                                                         | Keterangan                   |
|------------|--------------------------------------------------------------|------------------------------|
| id         | INT UNSIGNED AUTO_INCREMENT                                  | Primary key                  |
| servis_id  | INT UNSIGNED (FK → servis.id)                                | Servis yang diupdate         |
| status     | ENUM('Diterima','Sedang dicek','Perbaikan','Testing','Selesai') | Status baru               |
| catatan    | TEXT                                                         | Catatan tindakan teknisi     |
| foto       | VARCHAR(255)                                                 | Foto progres (opsional)      |
| updated_by | INT UNSIGNED (FK → users.id)                                 | Teknisi yang mengupdate      |
| updated_at | TIMESTAMP                                                    | Waktu update                 |

---

### Tabel `estimasi_harga`
Menyimpan patokan harga estimasi per kombinasi perangkat dan kerusakan.

| Kolom      | Tipe                                          | Keterangan                        |
|------------|-----------------------------------------------|-----------------------------------|
| id         | INT UNSIGNED AUTO_INCREMENT                   | Primary key                       |
| perangkat  | ENUM('macbook','windows','pc','imac','other') | Jenis perangkat                   |
| kerusakan  | ENUM('lcd','battery','ssd','thermal','other') | Jenis kerusakan                   |
| harga_min  | DECIMAL(12,0)                                 | Estimasi harga minimum (Rupiah)   |
| harga_max  | DECIMAL(12,0)                                 | Estimasi harga maksimum (Rupiah)  |
| keterangan | VARCHAR(255)                                  | Catatan tambahan estimasi         |

---

## Setup & Instalasi

### 1. Clone / copy project
Taruh folder `COST-APP` di:
```
C:/xampp/htdocs/COST-APP/
```

### 2. Jalankan XAMPP
Aktifkan **Apache** dan **MySQL** di XAMPP Control Panel.

### 3. Buat database
Buka `http://localhost/phpmyadmin`, buat database baru bernama `cost_db`.

### 4. Import SQL
Buka database `cost_db` → tab **SQL** → paste isi file SQL di bawah → klik **Go**.

### 5. Compile Tailwind
```bash
npm install
npx tailwindcss -i ./src/css/input.css -o ./dist/css/style.css --watch
```

### 6. Buka aplikasi
```
http://localhost/COST-APP/src/pages/index.php
```

---

## Akun Demo

| Role      | Email                  | Password |
|-----------|------------------------|----------|
| Pelanggan | pelanggan@geeko.com    | 123456   |
| Teknisi   | teknisi@geeko.com      | 123456   |

> **Catatan:** Hash password di atas adalah placeholder. Jalankan `generate_hash.php` untuk mendapatkan hash yang valid, lalu update kolom password di tabel `users`.

---

## Catatan Pengembangan

- Password disimpan menggunakan `password_hash()` dengan algoritma `PASSWORD_BCRYPT`
- Nomor tiket digenerate otomatis dengan format `GK-YYYYMMDD-XXXX`
- Data booking dari form estimasi dikirim ke halaman booking via `sessionStorage`
- Foto upload disimpan di `public/uploads/`
- Tailwind CSS di-compile via CLI, bukan CDN — pastikan selalu jalankan `--watch` saat development