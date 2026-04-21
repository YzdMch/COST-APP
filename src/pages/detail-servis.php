<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: /COST-APP/src/pages/dashboard-pelanggan.php');
    exit;
}

// Ambil data servis — pastikan milik pelanggan ini
$stmt = $pdo->prepare('
    SELECT * FROM servis
    WHERE id = ? AND user_id = ?
    LIMIT 1
');
$stmt->execute([$id, $_SESSION['user_id']]);
$servis = $stmt->fetch();

if (!$servis) {
    header('Location: /COST-APP/src/pages/dashboard-pelanggan.php');
    exit;
}

// Ambil riwayat log
$stmt = $pdo->prepare('
    SELECT sl.status, sl.catatan, sl.foto, sl.updated_at, u.nama AS teknisi
    FROM servis_log sl
    LEFT JOIN users u ON u.id = sl.updated_by
    WHERE sl.servis_id = ?
    ORDER BY sl.updated_at ASC
');
$stmt->execute([$id]);
$logs = $stmt->fetchAll();

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Servis • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">

  <!-- HEADER -->
<?php require_once __DIR__ . '/header.php'; ?>

  <main class="max-w-5xl mx-auto px-5 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Info Servis -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
          <i class="fas fa-info-circle text-primary-500 mr-2"></i>Detail Servis
        </h2>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Nomor Tiket</span>
            <span class="font-mono font-bold text-primary-700">
              <?= htmlspecialchars($servis['nomor_tiket']) ?>
            </span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Status</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass[$servis['status']] ?? '' ?>">
              <?= htmlspecialchars($servis['status']) ?>
            </span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Perangkat</span>
            <span class="font-semibold text-gray-800">
              <?= htmlspecialchars($labelPerangkat[$servis['perangkat']] ?? $servis['perangkat']) ?>
            </span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Kerusakan</span>
            <span class="font-semibold text-gray-800">
              <?= htmlspecialchars($labelKerusakan[$servis['jenis_kerusakan']] ?? $servis['jenis_kerusakan']) ?>
            </span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Cabang</span>
            <span class="font-semibold text-gray-800">Surabaya</span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Estimasi Biaya</span>
            <span class="font-bold text-primary-600">
              <?= $servis['estimasi_harga']
                  ? 'Rp ' . number_format($servis['estimasi_harga'], 0, ',', '.')
                  : 'Menunggu pengecekan' ?>
            </span>
          </div>
          <div class="flex justify-between border-b border-gray-100 pb-2">
            <span class="text-gray-500">Tanggal Masuk</span>
            <span class="text-gray-700">
              <?= date('d M Y, H:i', strtotime($servis['created_at'])) ?>
            </span>
          </div>
          <div>
            <span class="text-gray-500 block mb-1">Deskripsi Keluhan</span>
            <p class="text-gray-800 bg-gray-50 rounded-lg p-3">
              <?= nl2br(htmlspecialchars($servis['deskripsi'])) ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Riwayat Status -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
          <i class="fas fa-history text-primary-500 mr-2"></i>Riwayat Update
        </h2>

        <?php if (empty($logs)) : ?>
          <p class="text-gray-400 text-sm text-center py-8">Belum ada update dari teknisi.</p>
        <?php else : ?>
          <div class="relative">
            <!-- Garis timeline -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

            <div class="space-y-5">
              <?php foreach ($logs as $log) : ?>
                <div class="flex gap-4 relative">
                  <!-- Dot -->
                  <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10
                    <?= $log['status'] === 'Selesai' ? 'bg-green-500' : 'bg-primary-400' ?>">
                    <i class="fas fa-circle text-white text-xs"></i>
                  </div>
                  <!-- Konten -->
                  <div class="bg-gray-50 rounded-xl p-3 flex-1 text-sm">
                    <div class="flex items-center justify-between mb-1">
                      <span class="font-semibold text-gray-800"><?= htmlspecialchars($log['status']) ?></span>
                      <span class="text-xs text-gray-400">
                        <?= date('d M Y, H:i', strtotime($log['updated_at'])) ?>
                      </span>
                    </div>
                    <?php if ($log['catatan']) : ?>
                      <p class="text-gray-600"><?= nl2br(htmlspecialchars($log['catatan'])) ?></p>
                    <?php endif; ?>
                    <?php if ($log['teknisi']) : ?>
                      <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-user-cog mr-1"></i><?= htmlspecialchars($log['teknisi']) ?>
                      </p>
                    <?php endif; ?>
                    <?php if ($log['foto']) : ?>
                      <img
                        src="/COST-APP/public/uploads/<?= htmlspecialchars($log['foto']) ?>"
                        alt="Foto progres"
                        class="mt-2 rounded-lg max-h-32 object-cover"
                      >
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
  <!-- FOOTER -->
  <footer class="bg-gray-900 text-gray-300 py-8 mt-12 text-center text-sm">
    <p>© <?= date('Y') ?> Geeko Komputer. All rights reserved.</p>
  </footer>
</body>
</html>