<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Proteksi halaman — hanya teknisi yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

// Ambil semua data servis dari database
$stmt = $pdo->query('
    SELECT id, nomor_tiket, nama_pelanggan, perangkat, jenis_kerusakan, status, created_at
    FROM servis
    ORDER BY created_at DESC
');
$semuaServis = $stmt->fetchAll();

// Hitung statistik
$totalServis  = count($semuaServis);
$totalSelesai = count(array_filter($semuaServis, fn($s) => $s['status'] === 'Selesai'));
$totalProses  = $totalServis - $totalSelesai;

// Label tampilan
$labelPerangkat = [
    'macbook'  => 'MacBook Pro / Air',
    'windows'  => 'Windows Laptop',
    'pc'       => 'Desktop PC',
    'imac'     => 'iMac / Mac Desktop',
    'other'    => 'Lainnya',
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
    'Sedang dicek' => 'bg-yellow-100 text-yellow-800',
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
  <title>Dashboard Teknisi • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- HEADER -->
<?php require_once __DIR__ . '/header.php'; ?>

  <!-- MAIN -->
  <main class="max-w-7xl mx-auto px-4 py-8">

    <!-- Judul -->
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Teknisi</h1>
      <p class="text-gray-500">Kelola dan update progres perbaikan pelanggan</p>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Servis</p>
            <p class="text-2xl font-bold"><?= $totalServis ?></p>
          </div>
          <i class="fas fa-tools text-yellow-400 text-3xl"></i>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Selesai</p>
            <p class="text-2xl font-bold"><?= $totalSelesai ?></p>
          </div>
          <i class="fas fa-check-circle text-green-500 text-3xl"></i>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Dalam Proses</p>
            <p class="text-2xl font-bold"><?= $totalProses ?></p>
          </div>
          <i class="fas fa-spinner fa-pulse text-orange-500 text-3xl"></i>
        </div>
      </div>
    </div>

    <!-- Tabel Servis -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <h2 class="font-semibold text-lg">
          <i class="fas fa-list text-yellow-500 mr-2"></i> Daftar Servis Aktif
        </h2>
        <!-- Filter status -->
        <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <option value="">Semua Status</option>
          <option value="Diterima">Diterima</option>
          <option value="Sedang dicek">Sedang dicek</option>
          <option value="Perbaikan">Perbaikan</option>
          <option value="Testing">Testing</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Servis</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perangkat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kerusakan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody id="serviceTableBody" class="divide-y divide-gray-200">
            <?php if (empty($semuaServis)) : ?>
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                  <i class="fas fa-inbox text-4xl mb-2 block"></i>
                  Belum ada data servis masuk.
                </td>
              </tr>
            <?php else : ?>
              <?php foreach ($semuaServis as $servis) : ?>
                <tr class="hover:bg-gray-50 transition servis-row" data-status="<?= htmlspecialchars($servis['status']) ?>">
                  <td class="px-6 py-4 font-mono text-sm text-yellow-700 font-semibold">
                    <?= htmlspecialchars($servis['nomor_tiket']) ?>
                  </td>
                  <td class="px-6 py-4 text-sm"><?= htmlspecialchars($servis['nama_pelanggan']) ?></td>
                  <td class="px-6 py-4 text-sm"><?= htmlspecialchars($labelPerangkat[$servis['perangkat']] ?? $servis['perangkat']) ?></td>
                  <td class="px-6 py-4 text-sm"><?= htmlspecialchars($labelKerusakan[$servis['jenis_kerusakan']] ?? $servis['jenis_kerusakan']) ?></td>
                  <td class="px-6 py-4 text-sm">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass[$servis['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                      <?= htmlspecialchars($servis['status']) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <button
                      onclick="openModal(<?= $servis['id'] ?>, '<?= htmlspecialchars($servis['nomor_tiket']) ?>', '<?= htmlspecialchars($servis['nama_pelanggan']) ?>', '<?= htmlspecialchars($labelPerangkat[$servis['perangkat']] ?? $servis['perangkat']) ?>', '<?= htmlspecialchars($servis['status']) ?>')"
                      class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs transition"
                    >
                      <i class="fas fa-edit"></i> Update
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Update -->
  <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl max-w-lg w-full mx-4 p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800">
          <i class="fas fa-edit text-yellow-500"></i> Update Progres Servis
        </h3>
        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
          <i class="fas fa-times text-2xl"></i>
        </button>
      </div>
      <div class="mb-4 space-y-1 text-sm">
        <p><strong>Kode Servis:</strong> <span id="modalTicket" class="font-mono text-yellow-700"></span></p>
        <p><strong>Pelanggan:</strong> <span id="modalCustomer"></span></p>
        <p><strong>Perangkat:</strong> <span id="modalDevice"></span></p>
      </div>
      <form method="POST" action="/COST-APP/src/api/update-status.php" enctype="multipart/form-data">
        <input type="hidden" name="servis_id" id="modalServisId">
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Status Perbaikan</label>
          <select name="status" id="statusSelect" class="w-full border border-gray-300 rounded-xl py-2 px-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="Diterima">Diterima</option>
            <option value="Sedang dicek">Sedang dicek</option>
            <option value="Perbaikan">Perbaikan</option>
            <option value="Testing">Testing</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Catatan Tindakan</label>
          <textarea
            name="catatan"
            id="noteInput"
            rows="3"
            placeholder="Contoh: Ganti LCD, bersihkan debu..."
            class="w-full border border-gray-300 rounded-xl py-2 px-4 focus:outline-none focus:ring-2 focus:ring-yellow-400"
          ></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Upload Foto (opsional)</label>
          <input type="file" name="foto" accept="image/*" class="w-full text-gray-600 border border-gray-200 rounded-lg p-2 bg-gray-50">
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Batal</button>
          <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="bg-gray-900 text-gray-300 py-6 mt-12 text-center text-sm">
    <p>© 2024 Geeko Komputer. All rights reserved.</p>
  </footer>

  <script>
    // Burger menu
    document.getElementById('burger').addEventListener('click', function () {
      document.getElementById('mobileNav').classList.toggle('hidden');
    });

    // Buka modal update
    function openModal(id, tiket, pelanggan, perangkat, status) {
      document.getElementById('modalServisId').value = id;
      document.getElementById('modalTicket').textContent   = tiket;
      document.getElementById('modalCustomer').textContent = pelanggan;
      document.getElementById('modalDevice').textContent   = perangkat;
      document.getElementById('statusSelect').value        = status;
      document.getElementById('noteInput').value           = '';
      document.getElementById('updateModal').classList.remove('hidden');
    }

    // Tutup modal
    function closeModal() {
      document.getElementById('updateModal').classList.add('hidden');
    }

    // Tutup modal kalau klik backdrop
    document.getElementById('updateModal').addEventListener('click', function (e) {
      if (e.target === this) closeModal();
    });

    // Filter status
    document.getElementById('filterStatus').addEventListener('change', function () {
      const val  = this.value;
      const rows = document.querySelectorAll('.servis-row');
      rows.forEach(row => {
        if (!val || row.dataset.status === val) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>

</body>
</html>