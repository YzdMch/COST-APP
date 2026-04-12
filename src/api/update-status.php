<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Hanya teknisi yang boleh
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

$servis_id = (int) ($_POST['servis_id'] ?? 0);
$status    = trim($_POST['status']    ?? '');
$catatan   = trim($_POST['catatan']   ?? '');

$statusValid = ['Diterima', 'Sedang dicek', 'Perbaikan', 'Testing', 'Selesai'];
if (!$servis_id || !in_array($status, $statusValid)) {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

// Handle upload foto (opsional)
$foto = null;
if (!empty($_FILES['foto']['name'])) {
    $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array(strtolower($ext), $allowed)) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = uniqid('foto_') . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $filename);
        $foto = $filename;
    }
}

// Update status di tabel servis
$stmt = $pdo->prepare('UPDATE servis SET status = ? WHERE id = ?');
$stmt->execute([$status, $servis_id]);

// Simpan log ke tabel servis_log
$stmt = $pdo->prepare('INSERT INTO servis_log (servis_id, status, catatan, foto, updated_by) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$servis_id, $status, $catatan ?: null, $foto, $_SESSION['user_id']]);

header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
exit;