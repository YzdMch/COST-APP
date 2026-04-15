<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

// Ambil semua data servis
$stmt = $pdo->query('
    SELECT id, nomor_tiket, nama_pelanggan, perangkat, jenis_kerusakan, status, estimasi_harga, created_at
    FROM servis
    ORDER BY created_at DESC
');
$semuaServis = $stmt->fetchAll();

$totalServis  = count($semuaServis);
$totalSelesai = count(array_filter($semuaServis, fn($s) => $s['status'] === 'Selesai'));
$totalProses  = $totalServis - $totalSelesai;

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
    'Sedang dicek' => 'bg-yellow-100 text-yellow-800',
    'Perbaikan'    => 'bg-orange-100 text-orange-800',
    'Testing'      => 'bg-lime-100 text-lime-700',
    'Selesai'      => 'bg-green-100 text-green-800',
];

// Pesan sukses/error dari redirect
$pesan = $_GET['pesan'] ?? null;
$tiketHighlight = $_GET['tiket'] ?? null;
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
<body class="bg-gray-50 text-gray-800 font-sans">

  <?php require_once __DIR__ . '/header.php'; ?>

  <main class="max-w-7xl mx-auto px-4 py-8">

    <!-- Judul -->
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Teknisi</h1>
      <p class="text-gray-500">Kelola dan update progres perbaikan pelanggan</p>
    </div>

    <!-- Notifikasi -->
    <?php if ($pesan === 'hapus_berhasil') : ?>
      <div id="alertPesan" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4 mb-6 flex items-center justify-between">
        <span class="text-green-700 text-sm"><i class="fas fa-check-circle mr-2"></i>Data servis berhasil dihapus.</span>
        <button onclick="document.getElementById('alertPesan').remove()" class="text-green-500 hover:text-green-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
    <?php elseif ($pesan === 'edit_berhasil') : ?>
      <div id="alertPesan" class="bg-green-50 border-l-4 border-green-500 rounded-xl p-4 mb-6 flex items-center justify-between">
        <span class="text-green-700 text-sm"><i class="fas fa-check-circle mr-2"></i>Data servis berhasil diperbarui<?= $tiketHighlight ? ' untuk tiket ' . htmlspecialchars($tiketHighlight) : '' ?>.</span>
        <button onclick="document.getElementById('alertPesan').remove()" class="text-green-500 hover:text-green-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
    <?php endif; ?>

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
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h2 class="font-semibold text-lg">
          <i class="fas fa-list text-yellow-500 mr-2"></i>Daftar Servis Aktif
        </h2>
        <!-- Filter status -->
        <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
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
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Servis</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perangkat</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kerusakan</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody id="serviceTableBody" class="divide-y divide-gray-200">
            <?php if (empty($semuaServis)) : ?>
              <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                  <i class="fas fa-inbox text-4xl mb-2 block"></i>
                  Belum ada data servis masuk.
                </td>
              </tr>
            <?php else : ?>
              <?php foreach ($semuaServis as $s) : ?>
                <tr class="hover:bg-gray-50 transition servis-row" data-status="<?= htmlspecialchars($s['status']) ?>" data-tiket="<?= htmlspecialchars($s['nomor_tiket']) ?>">
                  <td class="px-4 py-4 font-mono text-sm text-yellow-700 font-semibold">
                    <?= htmlspecialchars($s['nomor_tiket']) ?>
                  </td>
                  <td class="px-4 py-4 text-sm"><?= htmlspecialchars($s['nama_pelanggan']) ?></td>
                  <td class="px-4 py-4 text-sm"><?= htmlspecialchars($labelPerangkat[$s['perangkat']] ?? $s['perangkat']) ?></td>
                  <td class="px-4 py-4 text-sm"><?= htmlspecialchars($labelKerusakan[$s['jenis_kerusakan']] ?? $s['jenis_kerusakan']) ?></td>
                  <td class="px-4 py-4 text-sm">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass[$s['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                      <?= htmlspecialchars($s['status']) ?>
                    </span>
                  </td>
                  <td class="px-4 py-4 text-sm">
                    <div class="flex flex-wrap items-center gap-2" style="min-width: 220px;">
                      <!-- Update status -->
                      <button
                        onclick="openModalUpdate(<?= $s['id'] ?>, '<?= htmlspecialchars($s['nomor_tiket']) ?>', '<?= htmlspecialchars($s['nama_pelanggan']) ?>', '<?= htmlspecialchars($labelPerangkat[$s['perangkat']] ?? $s['perangkat']) ?>', '<?= htmlspecialchars($s['status']) ?>')"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs transition inline-flex items-center gap-1 whitespace-nowrap"
                        title="Update Status">
                        <i class="fas fa-sync-alt"></i>
                        <span>Update</span>
                      </button>
                      <!-- Edit -->
                      <button type="button"
                        onclick="window.location.href='/COST-APP/src/pages/edit-servis.php?id=<?= $s['id'] ?>'"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1 rounded-lg text-xs transition inline-flex items-center gap-1 whitespace-nowrap"
                        title="Edit Data">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                      </button>
                      <!-- Hapus -->
                      <button
                        onclick="openModalHapus(<?= $s['id'] ?>, '<?= htmlspecialchars($s['nomor_tiket']) ?>')"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs transition inline-flex items-center gap-1 whitespace-nowrap"
                        title="Hapus Data">
                        <i class="fas fa-trash"></i>
                        <span>Hapus</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- ===== MODAL UPDATE STATUS ===== -->
  <div id="modalUpdate" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden px-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800">
          <i class="fas fa-sync-alt text-yellow-500 mr-2"></i>Update Progres Servis
        </h3>
        <button onclick="closeModalUpdate()" class="text-gray-400 hover:text-gray-600">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <div class="mb-4 space-y-1 text-sm bg-gray-50 rounded-lg p-3">
        <p><strong>Kode:</strong> <span id="uTiket" class="font-mono text-yellow-700"></span></p>
        <p><strong>Pelanggan:</strong> <span id="uPelanggan"></span></p>
        <p><strong>Perangkat:</strong> <span id="uPerangkat"></span></p>
      </div>
      <form method="POST" action="/COST-APP/src/api/update-status.php" enctype="multipart/form-data">
        <input type="hidden" name="servis_id" id="uServisId">
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Status Perbaikan</label>
          <select name="status" id="uStatus" class="w-full border border-gray-300 rounded-xl py-2 px-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            <option value="Diterima">Diterima</option>
            <option value="Sedang dicek">Sedang dicek</option>
            <option value="Perbaikan">Perbaikan</option>
            <option value="Testing">Testing</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Catatan Tindakan</label>
          <textarea name="catatan" rows="3" placeholder="Contoh: Ganti LCD, bersihkan debu..."
            class="w-full border border-gray-300 rounded-xl py-2 px-4 focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Upload Foto (opsional)</label>
          <input type="file" name="foto" accept="image/*"
            class="w-full text-gray-600 border border-gray-200 rounded-lg p-2 bg-gray-50">
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" onclick="closeModalUpdate()"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Batal</button>
          <button type="submit"
            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
            <i class="fas fa-save mr-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== MODAL KONFIRMASI HAPUS ===== -->
  <div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden px-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
      <div class="text-center mb-5">
        <div class="inline-flex items-center justify-center bg-red-100 p-4 rounded-full mb-3">
          <i class="fas fa-trash text-red-500 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Hapus Data Servis?</h3>
        <p class="text-gray-500 text-sm mt-2">
          Anda akan menghapus tiket <strong id="hTiket" class="text-red-600 font-mono"></strong>.
          <br>Tindakan ini tidak dapat dibatalkan.
        </p>
      </div>
      <form method="POST" action="/COST-APP/src/api/hapus-servis.php">
        <input type="hidden" name="servis_id" id="hServisId">
        <div class="flex gap-3 justify-center">
          <button type="button" onclick="closeModalHapus()"
            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition font-semibold">
            Batal
          </button>
          <button type="submit"
            class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-semibold">
            <i class="fas fa-trash mr-1"></i>Ya, Hapus
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
    // Filter status
    document.getElementById('filterStatus').addEventListener('change', function () {
      const val  = this.value;
      document.querySelectorAll('.servis-row').forEach(row => {
        row.style.display = (!val || row.dataset.status === val) ? '' : 'none';
      });
    });

    // Modal Update Status
    function openModalUpdate(id, tiket, pelanggan, perangkat, status) {
      document.getElementById('uServisId').value          = id;
      document.getElementById('uTiket').textContent       = tiket;
      document.getElementById('uPelanggan').textContent   = pelanggan;
      document.getElementById('uPerangkat').textContent   = perangkat;
      document.getElementById('uStatus').value            = status;
      document.getElementById('modalUpdate').classList.remove('hidden');
    }
    function closeModalUpdate() {
      document.getElementById('modalUpdate').classList.add('hidden');
    }
    document.getElementById('modalUpdate').addEventListener('click', function (e) {
      if (e.target === this) closeModalUpdate();
    });

    // Modal Hapus
    function openModalHapus(id, tiket) {
      document.getElementById('hServisId').value    = id;
      document.getElementById('hTiket').textContent = tiket;
      document.getElementById('modalHapus').classList.remove('hidden');
    }
    function closeModalHapus() {
      document.getElementById('modalHapus').classList.add('hidden');
    }
    document.getElementById('modalHapus').addEventListener('click', function (e) {
      if (e.target === this) closeModalHapus();
    });

    const highlightTicket = '<?= htmlspecialchars($tiketHighlight) ?>';
    if (highlightTicket) {
      const targetRow = document.querySelector(`.servis-row[data-tiket="${highlightTicket}"]`);
      if (targetRow) {
        targetRow.classList.add('bg-yellow-50', 'border-l-4', 'border-yellow-400');
        targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  </script>

</body>
</html>