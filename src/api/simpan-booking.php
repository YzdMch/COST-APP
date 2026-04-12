<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /COST-APP/src/pages/booking.php');
    exit;
}

$nama      = trim($_POST['nama']       ?? '');
$email     = trim($_POST['email']      ?? '');
$telepon   = trim($_POST['no_telepon'] ?? '');
$perangkat = trim($_POST['perangkat']  ?? '');
$kerusakan = trim($_POST['kerusakan']  ?? '');
$cabang    = trim($_POST['cabang']     ?? '');
$deskripsi = trim($_POST['deskripsi']  ?? '');

// Validasi sederhana
if (!$nama || !$email || !$perangkat || !$kerusakan || !$cabang || !$deskripsi) {
    header('Location: /COST-APP/src/pages/booking.php');
    exit;
}

// Ambil estimasi harga dari database
$stmt = $pdo->prepare('SELECT harga_min, harga_max FROM estimasi_harga WHERE perangkat = ? AND kerusakan = ? LIMIT 1');
$stmt->execute([$perangkat, $kerusakan]);
$estimasi = $stmt->fetch();
$harga_min = $estimasi['harga_min'] ?? null;
$harga_max = $estimasi['harga_max'] ?? null;

// Generate nomor tiket unik: GK-YYYYMMDD-XXXX
$tanggal     = date('Ymd');
$randomSuffix = strtoupper(substr(uniqid(), -4));
$nomorTiket  = 'GK-' . $tanggal . '-' . $randomSuffix;

// Simpan ke tabel servis
$stmt = $pdo->prepare('
    INSERT INTO servis 
        (nomor_tiket, user_id, nama_pelanggan, email, no_telepon, perangkat, jenis_kerusakan, cabang, deskripsi, estimasi_harga, status)
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $nomorTiket,
    $_SESSION['user_id'],
    $nama,
    $email,
    $telepon,
    $perangkat,
    $kerusakan,
    $cabang,
    $deskripsi,
    $harga_max, // simpan harga max sebagai estimasi awal
    'Diterima',
]);

// Simpan log awal
$servisId = $pdo->lastInsertId();
$stmt = $pdo->prepare('INSERT INTO servis_log (servis_id, status, catatan, updated_by) VALUES (?, ?, ?, ?)');
$stmt->execute([$servisId, 'Diterima', 'Booking baru masuk dari pelanggan.', $_SESSION['user_id']]);

// Redirect ke dashboard pelanggan dengan tiket
header('Location: /COST-APP/src/pages/dashboard-pelanggan.php?tiket=' . urlencode($nomorTiket));
exit;