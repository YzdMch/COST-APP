<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Hanya pelanggan yang sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

// Ambil semua servis milik pelanggan ini
$stmt = $pdo->prepare('
    SELECT id, nomor_tiket, perangkat, jenis_kerusakan, cabang, status, estimasi_harga, created_at
    FROM servis
    WHERE user_id = ?
    ORDER BY created_at DESC
');
$stmt->execute([$_SESSION['user_id']]);
$semuaServis = $stmt->fetchAll();

// Tiket baru dari redirect booking
$tiketBaru = $_GET['tiket'] ?? null;

$labelPerangkat = [
    'macbook' => 'MacBook Pro / Air',
    'windows' => 'Windows Laptop',
    'pc'      => 'Desktop PC',
    'imac'    => 'iMac / Mac Desktop',
    'other'   => 'Lainnya',
];
$labelKerusakan = [
    'lcd'     => 'Layar Pecah / LCD Rusak',
    'battery' => 'Baterai Kembang / Drop',
    'ssd'     => 'Upgrade SSD',
    'thermal' => 'Thermal Paste / Cleaning',
    'other'   => 'Lainnya',
];
$statusClass = [
    'Diterima'     => 'bg-blue-100 text-blue-700',
    'Sedang dicek' => 'bg-primary-100 text-primary-800',
    'Perbaikan'    => 'bg-orange-100 text-orange-800',
    'Testing'      => 'bg-lime-100 text-lime-700',
    'Selesai'      => 'bg-green-100 text-green-800',
];
$statusIcon = [
    'Diterima'     => 'fa-inbox',
    'Sedang dicek' => 'fa-search',
    'Perbaikan'    => 'fa-wrench',
    'Testing'      => 'fa-vial',
    'Selesai'      => 'fa-check-circle',
];

// Statistik
$total    = count($semuaServis);
$selesai  = count(array_filter($semuaServis, fn($s) => $s['status'] === 'Selesai'));
$proses   = $total - $selesai;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pelanggan • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">

  <!-- HEADER -->
<?php require_once __DIR__ . '/header.php'; ?>

  <main class="max-w-7xl mx-auto px-5 py-10">

    <!-- Greeting -->
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
        Halo, <?= htmlspecialchars($_SESSION['nama']) ?>!
      </h1>
      <p class="text-gray-500 mt-1">Pantau status servis perangkat Anda di sini.</p>
    </div>

    <!-- Popup tiket baru -->
    <?php if ($tiketBaru) : ?>
      <div id="alertTiket" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-5 mb-6 flex items-start gap-4">
        <i class="fas fa-check-circle text-green-500 text-2xl mt-1"></i>
        <div>
          <p class="font-bold text-gray-800">Booking berhasil!</p>
          <p class="text-gray-600 text-sm mt-1">
            Nomor tiket Anda:
            <span class="font-mono font-bold text-primary-700 text-base">
              <?= htmlspecialchars($tiketBaru) ?>
            </span>
          </p>
          <p class="text-gray-500 text-xs mt-1">Simpan nomor tiket ini untuk melacak status servis Anda.</p>
        </div>
        <button onclick="document.getElementById('alertTiket').remove()"
          class="ml-auto text-gray-400 hover:text-gray-600">
          <i class="fas fa-times"></i>
        </button>
      </div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-primary-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Booking</p>
            <p class="text-2xl font-bold"><?= $total ?></p>
          </div>
          <i class="fas fa-ticket-alt text-primary-400 text-3xl"></i>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Selesai</p>
            <p class="text-2xl font-bold"><?= $selesai ?></p>
          </div>
          <i class="fas fa-check-circle text-green-500 text-3xl"></i>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Dalam Proses</p>
            <p class="text-2xl font-bold"><?= $proses ?></p>
          </div>
          <i class="fas fa-spinner fa-pulse text-orange-500 text-3xl"></i>
        </div>
      </div>
    </div>

    <!-- Daftar Servis -->
    <?php if (empty($semuaServis)) : ?>
      <div class="bg-white rounded-2xl shadow p-12 text-center">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4 block"></i>
        <p class="text-gray-500 text-lg font-semibold">Belum ada booking</p>
        <p class="text-gray-400 text-sm mt-1 mb-6">Mulai booking servis perangkat Anda sekarang.</p>
        <a href="/COST-APP/src/pages/perhitungan.php"
          class="bg-primary-500 hover:bg-primary-600 text-white font-bold px-6 py-3 rounded-xl transition">
          <i class="fas fa-plus mr-2"></i>Booking Sekarang
        </a>
      </div>
    <?php else : ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <?php foreach ($semuaServis as $servis) : ?>
          <div class="bg-white rounded-2xl shadow hover:shadow-md transition overflow-hidden">

            <!-- Card header -->
            <div class="bg-gray-50 px-5 py-4 flex items-center justify-between border-b border-gray-100">
              <span class="font-mono font-bold text-primary-700 text-sm">
                <?= htmlspecialchars($servis['nomor_tiket']) ?>
              </span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass[$servis['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                <i class="fas <?= $statusIcon[$servis['status']] ?? 'fa-circle' ?> mr-1"></i>
                <?= htmlspecialchars($servis['status']) ?>
              </span>
            </div>

            <!-- Card body -->
            <div class="px-5 py-4 space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500">Perangkat</span>
                <span class="font-semibold text-gray-800">
                  <?= htmlspecialchars($labelPerangkat[$servis['perangkat']] ?? $servis['perangkat']) ?>
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Kerusakan</span>
                <span class="font-semibold text-gray-800">
                  <?= htmlspecialchars($labelKerusakan[$servis['jenis_kerusakan']] ?? $servis['jenis_kerusakan']) ?>
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Estimasi Biaya</span>
                <span class="font-bold text-primary-600">
                  <?= $servis['estimasi_harga']
                      ? 'Rp ' . number_format($servis['estimasi_harga'], 0, ',', '.')
                      : '-' ?>
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Tanggal Masuk</span>
                <span class="text-gray-700">
                  <?= date('d M Y', strtotime($servis['created_at'])) ?>
                </span>
              </div>
            </div>

            <!-- Card footer — lihat detail & riwayat -->
            <div class="px-5 py-3 border-t border-gray-100 flex justify-end">
              <a href="/COST-APP/src/pages/detail-servis.php?id=<?= $servis['id'] ?>"
                class="text-primary-600 hover:text-primary-700 text-sm font-semibold">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-900 text-gray-300 py-8 mt-12 text-center text-sm">
    <p>© <?= date('Y') ?> Geeko Komputer. All rights reserved.</p>
  </footer>

</body>
</html>