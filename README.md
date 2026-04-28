# Geeko Komputer — Aplikasi Estimasi & Manajemen Servis

Aplikasi web berbasis PHP + MySQL untuk mengelola estimasi biaya servis komputer secara transparan. Pelanggan dapat mengisi form estimasi, melakukan booking servis, dan memantau status perbaikan secara real-time. Teknisi dapat mengelola dan mengupdate status servis dari dashboard khusus.

---

## Teknologi yang Digunakan

- **Frontend**: HTML, Tailwind CSS (compiled via CLI)
- **Backend**: PHP (Native, PDO)
- **Database**: MySQL / MariaDB
- **Server**: XAMPP / Laragon / Docker
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

## Struktur Folder

```
COST-APP/
├── config/
│   └── db.php              # Konfigurasi database (auto-detect Docker/XAMPP)
├── docker/
│   ├── apache.conf          # Config Apache untuk Docker
│   └── init.sql             # SQL schema + data awal (auto-import di Docker)
├── dist/
│   └── css/
│       └── style.css        # Output Tailwind CSS (hasil compile)
├── public/
│   └── images/              # Logo, foto developer, dll
├── src/
│   ├── api/                 # Backend PHP (auth, booking, update status, dll)
│   ├── css/
│   │   └── input.css        # Input Tailwind CSS
│   └── pages/               # Halaman PHP (index, login, register, dashboard, dll)
├── docker-compose.yml       # Docker Compose config
├── Dockerfile               # Docker build config
├── tailwind.config.js       # Config Tailwind CSS
└── package.json             # Node.js dependencies (Tailwind)
```

---

## Setup & Instalasi

Ada **2 cara** menjalankan project ini. Pilih salah satu sesuai environment kamu:

---

### Opsi A: XAMPP / Laragon (Paling Simpel)

> Cocok untuk teman-teman yang sudah terbiasa pakai XAMPP atau Laragon.

#### 1. Clone / Copy Project

```bash
git clone https://github.com/YzdMch/COST-APP.git
```

Pindahkan folder `COST-APP` ke folder htdocs:

| Software | Lokasi |
|----------|--------|
| XAMPP | `C:/xampp/htdocs/COST-APP/` |
| Laragon | `C:/laragon/www/COST-APP/` |

#### 2. Jalankan Apache & MySQL

- **XAMPP**: Buka XAMPP Control Panel → klik **Start** pada **Apache** dan **MySQL**
- **Laragon**: Klik **Start All**

#### 3. Buat Database

Buka browser → akses **phpMyAdmin**:

| Software | URL phpMyAdmin |
|----------|----------------|
| XAMPP | `http://localhost/phpmyadmin` |
| Laragon | `http://localhost/phpmyadmin` |

Lalu:
1. Klik **New** (di sidebar kiri)
2. Nama database: `cost_db`
3. Collation: `utf8mb4_general_ci`
4. Klik **Create**

#### 4. Import SQL

1. Klik database `cost_db` di sidebar
2. Klik tab **Import**
3. Klik **Choose File** → pilih file `docker/init.sql` dari folder project
4. Klik **Go**

> **Atau** cara manual: klik tab **SQL** → paste isi file `docker/init.sql` → klik **Go**

#### 5. Install Dependencies & Compile Tailwind CSS

Buka terminal / command prompt di folder project:

```bash
npm install
npx tailwindcss -i ./src/css/input.css -o ./dist/css/style.css --watch
```

> `--watch` artinya Tailwind akan otomatis compile ulang setiap ada perubahan file. Biarkan terminal ini tetap terbuka saat development.

#### 6. Buka Aplikasi

```
http://localhost/COST-APP/src/pages/index.php
```

✅ **Selesai!** Aplikasi siap digunakan.

---

### Opsi B: Docker (WSL / Linux / Mac)

> Cocok untuk yang sudah install Docker. Tidak perlu install XAMPP, PHP, MySQL, atau Node.js secara manual — semua sudah di dalam container.

#### Prasyarat

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) sudah terinstall dan running
- (Windows) WSL 2 sudah aktif

#### 1. Clone Project

```bash
git clone https://github.com/YzdMch/COST-APP.git
cd COST-APP
```

#### 2. Jalankan Docker

```bash
docker compose up --build -d
```

Tunggu sampai selesai. Perintah ini akan:
- Build Tailwind CSS secara otomatis
- Menjalankan Apache + PHP
- Menjalankan MySQL + import database otomatis
- Menjalankan phpMyAdmin

#### 3. Buka Aplikasi

| Service | URL | Keterangan |
|---------|-----|------------|
| **Aplikasi** | `http://localhost:8080/COST-APP/src/pages/index.php` | Halaman utama |
| **phpMyAdmin** | `http://localhost:8081` | User: `root`, Pass: `root` |

✅ **Selesai!** Tidak perlu setup database manual — `init.sql` sudah otomatis di-import.

#### Perintah Docker yang Berguna

```bash
# Start (jika sudah pernah build)
docker compose up -d

# Stop (data database TETAP tersimpan)
docker compose down

# Stop + HAPUS database (reset dari awal)
docker compose down -v

# Rebuild (setelah ubah code)
docker compose up --build -d

# Lihat status container
docker compose ps

# Lihat log error
docker compose logs web
docker compose logs db
```

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Pelanggan | pelanggan@geeko.com | 123456 |
| Teknisi | teknisi@geeko.com | 123456 |

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

## Catatan Pengembangan

- Password disimpan menggunakan `password_hash()` dengan algoritma `PASSWORD_BCRYPT`
- Nomor tiket digenerate otomatis dengan format `GK-YYYYMMDD-XXXX`
- Data booking dari form estimasi dikirim ke halaman booking via `sessionStorage`
- Foto upload disimpan di `public/uploads/`
- Tailwind CSS di-compile via CLI, bukan CDN
- `config/db.php` otomatis mendeteksi environment (Docker / XAMPP / Laragon) — tidak perlu diubah manual
- File `docker/init.sql` bisa digunakan untuk import database baik di Docker maupun di phpMyAdmin XAMPP/Laragon

---

## Troubleshooting

### CSS tidak muncul / halaman polos
- **XAMPP/Laragon**: Pastikan sudah jalankan `npx tailwindcss -i ./src/css/input.css -o ./dist/css/style.css --watch`
- **Docker**: Jalankan `docker compose up --build -d` (pastikan pakai `--build`)

### Tidak bisa login
- Pastikan database `cost_db` sudah ada dan tabel `users` sudah ter-import
- Cek di phpMyAdmin apakah data user demo sudah masuk

### Database connection error
- **XAMPP**: Pastikan MySQL sudah running di XAMPP Control Panel
- **Docker**: Jalankan `docker compose logs db` untuk lihat error MySQL

### Port sudah dipakai (Docker)
- Ubah port di `docker-compose.yml`, misalnya `"8090:80"` untuk web atau `"8082:80"` untuk phpMyAdmin