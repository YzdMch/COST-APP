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

$id              = (int)   ($_POST['id']              ?? 0);
$nama_pelanggan  = trim(    $_POST['nama_pelanggan']  ?? '');
$email           = trim(    $_POST['email']           ?? '');
$no_telepon      = trim(    $_POST['no_telepon']      ?? '');
$perangkat       = trim(    $_POST['perangkat']       ?? '');
$jenis_kerusakan = trim(    $_POST['jenis_kerusakan'] ?? '');
$status          = trim(    $_POST['status']          ?? '');
$estimasi_harga  = !empty($_POST['estimasi_harga']) ? (float) $_POST['estimasi_harga'] : null;
$deskripsi       = trim(    $_POST['deskripsi']       ?? '');

// Validasi server-side
$error = null;

if (!$id) $error = 'Data tidak valid.';
elseif (strlen($nama_pelanggan) < 3) $error = 'Nama minimal 3 karakter.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Format email tidak valid.';
elseif (strlen($no_telepon) < 9) $error = 'Nomor telepon tidak valid.';
elseif (strlen($deskripsi) < 5) $error = 'Deskripsi terlalu pendek.';

if ($error) {
    header('Location: /COST-APP/src/pages/edit-servis.php?id=' . $id . '&error=' . urlencode($error));
    exit;
}

// Update ke database
$stmt = $pdo->prepare('
    UPDATE servis SET
        nama_pelanggan  = ?,
        email           = ?,
        no_telepon      = ?,
        perangkat       = ?,
        jenis_kerusakan = ?,
        status          = ?,
        estimasi_harga  = ?,
        deskripsi       = ?
    WHERE id = ?
');
$stmt->execute([
    $nama_pelanggan,
    $email,
    $no_telepon,
    $perangkat,
    $jenis_kerusakan,
    $status,
    $estimasi_harga,
    $deskripsi,
    $id,
]);

// Catat di log kalau status berubah
$stmtCek = $pdo->prepare('SELECT status FROM servis WHERE id = ? LIMIT 1');
$stmtCek->execute([$id]);
$stmtLog = $pdo->prepare('INSERT INTO servis_log (servis_id, status, catatan, updated_by) VALUES (?, ?, ?, ?)');
$stmtLog->execute([$id, $status, 'Data servis diperbarui oleh teknisi.', $_SESSION['user_id']]);

$stmtTicket = $pdo->prepare('SELECT nomor_tiket FROM servis WHERE id = ? LIMIT 1');
$stmtTicket->execute([$id]);
$nomorTiket = $stmtTicket->fetchColumn();

$redirectUrl = '/COST-APP/src/pages/dashboard-teknisi.php?pesan=edit_berhasil';
if ($nomorTiket) {
    $redirectUrl .= '&tiket=' . urlencode($nomorTiket);
}

header('Location: ' . $redirectUrl);
exit;