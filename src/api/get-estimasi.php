<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$perangkat = $input['perangkat'] ?? '';
$kerusakan = $input['kerusakan'] ?? '';

$perangkatValid = ['macbook', 'windows', 'pc', 'imac', 'other'];
$kerusakanValid = ['lcd', 'battery', 'ssd', 'thermal', 'other'];

if (!in_array($perangkat, $perangkatValid) || !in_array($kerusakan, $kerusakanValid)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit;
}

$stmt = $pdo->prepare('SELECT harga_min, harga_max, keterangan FROM estimasi_harga WHERE perangkat = ? AND kerusakan = ? LIMIT 1');
$stmt->execute([$perangkat, $kerusakan]);
$data = $stmt->fetch();

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Estimasi tidak ditemukan.']);
    exit;
}

echo json_encode([
    'status'     => 'ok',
    'harga_min'  => $data['harga_min'],
    'harga_max'  => $data['harga_max'],
    'keterangan' => $data['keterangan'],
]);