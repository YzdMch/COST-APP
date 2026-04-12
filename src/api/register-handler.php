<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /COST-APP/src/pages/register.php');
    exit;
}

$nama      = trim($_POST['nama']                ?? '');
$email     = trim($_POST['email']               ?? '');
$telepon   = trim($_POST['no_telepon']          ?? '');
$password  = trim($_POST['password']            ?? '');
$konfirm   = trim($_POST['konfirmasi_password'] ?? '');

$error = null;
$old   = compact('nama', 'email', 'telepon');

// Validasi server-side
if (strlen($nama) < 3) {
    $error = 'Nama minimal 3 karakter.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Format email tidak valid.';
} elseif (strlen($telepon) < 9) {
    $error = 'Nomor telepon tidak valid.';
} elseif (strlen($password) < 6) {
    $error = 'Password minimal 6 karakter.';
} elseif ($password !== $konfirm) {
    $error = 'Konfirmasi password tidak cocok.';
}

// Cek email sudah terdaftar
if (!$error) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = 'Email sudah terdaftar. Silakan login.';
    }
}

// Jika ada error, kembali ke halaman register
if ($error) {
    require __DIR__ . '/../pages/register.php';
    exit;
}

// Simpan user baru
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO users (nama, email, no_telepon, password, role) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$nama, $email, $telepon, $hash, 'pelanggan']);

$userId = $pdo->lastInsertId();

// Auto login setelah register
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['nama']    = $nama;
$_SESSION['email']   = $email;
$_SESSION['role']    = 'pelanggan';

// Kalau dari redirect booking, langsung ke booking
$redirect = $_GET['redirect'] ?? '';
if ($redirect === 'booking') {
    header('Location: /COST-APP/src/pages/booking.php');
} else {
    header('Location: /COST-APP/src/pages/dashboard-pelanggan.php');
}
exit;