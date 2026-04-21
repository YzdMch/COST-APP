<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /COST-APP/src/pages/login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

// Ambil data servis
$stmt = $pdo->prepare('SELECT * FROM servis WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$servis = $stmt->fetch();

if (!$servis) {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Servis • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">

  <?php require_once __DIR__ . '/header.php'; ?>

  <section class="py-12 px-5">
    <div class="container mx-auto max-w-3xl">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 md:p-10">

          <!-- Heading -->
          <div class="flex items-center gap-3 mb-8">
            <a href="/COST-APP/src/pages/dashboard-teknisi.php"
              class="text-gray-400 hover:text-gray-600 transition">
              <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
              <h1 class="text-2xl font-extrabold text-gray-800">Edit Data Servis</h1>
              <p class="text-gray-500 text-sm">
                Tiket: <span class="font-mono text-primary-700"><?= htmlspecialchars($servis['nomor_tiket']) ?></span>
              </p>
            </div>
          </div>

          <?php if ($error) : ?>
            <div class="bg-red-100 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl mb-6">
              <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form id="editForm" method="POST" action="/COST-APP/src/api/edit-servis.php">
            <input type="hidden" name="id" value="<?= $servis['id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

              <!-- Nama Pelanggan -->
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Nama Pelanggan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                  value="<?= htmlspecialchars($servis['nama_pelanggan']) ?>"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary-400 transition">
                <div id="namaError" class="text-red-500 text-sm mt-1 hidden"></div>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email"
                  value="<?= htmlspecialchars($servis['email']) ?>"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary-400 transition">
                <div id="emailError" class="text-red-500 text-sm mt-1 hidden"></div>
              </div>

              <!-- No Telepon -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">No. Telepon <span class="text-red-500">*</span></label>
                <input type="tel" name="no_telepon" id="no_telepon"
                  value="<?= htmlspecialchars($servis['no_telepon']) ?>"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary-400 transition">
                <div id="teleponError" class="text-red-500 text-sm mt-1 hidden"></div>
              </div>

              <!-- Perangkat -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Perangkat <span class="text-red-500">*</span></label>
                <select name="perangkat" id="perangkat"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
                  <option value="macbook" <?= $servis['perangkat'] === 'macbook' ? 'selected' : '' ?>>MacBook Pro / Air</option>
                  <option value="windows" <?= $servis['perangkat'] === 'windows' ? 'selected' : '' ?>>Windows Laptop</option>
                  <option value="pc"      <?= $servis['perangkat'] === 'pc'      ? 'selected' : '' ?>>Desktop PC</option>
                  <option value="imac"    <?= $servis['perangkat'] === 'imac'    ? 'selected' : '' ?>>iMac / Mac Desktop</option>
                  <option value="other"   <?= $servis['perangkat'] === 'other'   ? 'selected' : '' ?>>Lainnya</option>
                </select>
              </div>

              <!-- Kerusakan -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Jenis Kerusakan <span class="text-red-500">*</span></label>
                <select name="jenis_kerusakan" id="jenis_kerusakan"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
                  <option value="lcd"     <?= $servis['jenis_kerusakan'] === 'lcd'     ? 'selected' : '' ?>>Layar Pecah / LCD Rusak</option>
                  <option value="battery" <?= $servis['jenis_kerusakan'] === 'battery' ? 'selected' : '' ?>>Baterai Kembang / Drop</option>
                  <option value="ssd"     <?= $servis['jenis_kerusakan'] === 'ssd'     ? 'selected' : '' ?>>Upgrade SSD</option>
                  <option value="thermal" <?= $servis['jenis_kerusakan'] === 'thermal' ? 'selected' : '' ?>>Thermal Paste / Cleaning</option>
                  <option value="other"   <?= $servis['jenis_kerusakan'] === 'other'   ? 'selected' : '' ?>>Lainnya</option>
                </select>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <select name="status"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 bg-white focus:outline-none focus:ring-2 focus:ring-primary-400">
                  <option value="Diterima"     <?= $servis['status'] === 'Diterima'     ? 'selected' : '' ?>>Diterima</option>
                  <option value="Sedang dicek" <?= $servis['status'] === 'Sedang dicek' ? 'selected' : '' ?>>Sedang dicek</option>
                  <option value="Perbaikan"    <?= $servis['status'] === 'Perbaikan'    ? 'selected' : '' ?>>Perbaikan</option>
                  <option value="Testing"      <?= $servis['status'] === 'Testing'      ? 'selected' : '' ?>>Testing</option>
                  <option value="Selesai"      <?= $servis['status'] === 'Selesai'      ? 'selected' : '' ?>>Selesai</option>
                </select>
              </div>

              <!-- Estimasi Harga -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Estimasi Harga (Rp)</label>
                <input type="number" name="estimasi_harga"
                  value="<?= htmlspecialchars($servis['estimasi_harga'] ?? '') ?>"
                  placeholder="Contoh: 1500000"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary-400 transition">
              </div>

              <!-- Deskripsi -->
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Deskripsi Keluhan <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                  class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary-400"
                  ><?= htmlspecialchars($servis['deskripsi']) ?></textarea>
                <div id="deskripsiError" class="text-red-500 text-sm mt-1 hidden"></div>
              </div>

            </div>

            <!-- Tombol -->
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
              <a href="/COST-APP/src/pages/dashboard-teknisi.php"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition text-center font-semibold">
                Batal
              </a>
              <button type="submit"
                class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-xl transition">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-gray-900 text-gray-300 py-6 mt-4 text-center text-sm">
    <p>© <?= date('Y') ?> Geeko Komputer. All rights reserved.</p>
  </footer>

  <script>
    document.getElementById('editForm').addEventListener('submit', function (e) {
      let valid = true;

      const nama     = document.getElementById('nama_pelanggan');
      const email    = document.getElementById('email');
      const telepon  = document.getElementById('no_telepon');
      const deskripsi = document.getElementById('deskripsi');

      const namaErr    = document.getElementById('namaError');
      const emailErr   = document.getElementById('emailError');
      const telErr     = document.getElementById('teleponError');
      const deskErr    = document.getElementById('deskripsiError');

      [namaErr, emailErr, telErr, deskErr].forEach(el => el.classList.add('hidden'));

      if (nama.value.trim().length < 3) {
        namaErr.textContent = 'Nama minimal 3 karakter';
        namaErr.classList.remove('hidden');
        valid = false;
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        emailErr.textContent = 'Format email tidak valid';
        emailErr.classList.remove('hidden');
        valid = false;
      }
      if (telepon.value.trim().length < 9) {
        telErr.textContent = 'Nomor telepon tidak valid';
        telErr.classList.remove('hidden');
        valid = false;
      }
      if (deskripsi.value.trim().length < 5) {
        deskErr.textContent = 'Deskripsi terlalu pendek';
        deskErr.classList.remove('hidden');
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  </script>

</body>
</html>