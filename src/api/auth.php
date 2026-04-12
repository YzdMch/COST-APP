<?php
session_start();

// Path dari src/api/auth.php ke config/db.php
require_once __DIR__ . '/../../config/db.php';

// Kalau sudah login, langsung redirect sesuai role
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header('Location: ' . ($role === 'teknisi' ? '/COST-APP/src/pages/dashboard-teknisi.php' : '/COST-APP/src/pages/index.php'));
    exit;
}

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

// Ambil & bersihkan input
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']);

// Validasi server-side
$error     = null;
$old_email = $email;

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Format email tidak valid.';
}

if (!$error && strlen($password) < 6) {
    $error = 'Password minimal 6 karakter.';
}

if (!$error) {
    $stmt = $pdo->prepare('SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Email atau password salah.';
    }
}

// Jika ada error, tampilkan kembali halaman login
if ($error) {
    require __DIR__ . '/../pages/login.php';
    exit;
}

// Login berhasil — simpan ke session
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama']    = $user['nama'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

// Fitur "Ingat saya" — cookie 30 hari
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

// Redirect sesuai role
if ($user['role'] === 'teknisi') {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
} else {
    header('Location: /COST-APP/src/pages/index.php');
}
exit;