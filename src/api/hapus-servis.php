<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

$id = (int) ($_POST['servis_id'] ?? 0);

if (!$id) {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

// Pastikan data ada sebelum dihapus
$stmt = $pdo->prepare('SELECT id FROM servis WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

// Hapus servis (servis_log ikut terhapus karena ON DELETE CASCADE)
$stmt = $pdo->prepare('DELETE FROM servis WHERE id = ?');
$stmt->execute([$id]);

header('Location: /COST-APP/src/pages/dashboard-teknisi.php?pesan=hapus_berhasil');
exit;