<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Hanya pelanggan yang sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /COST-APP/src/pages/login.php?redirect=booking');
    exit;
}
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /COST-APP/src/pages/dashboard-teknisi.php');
    exit;
}

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Booking • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">

  <!-- HEADER -->
<?php require_once __DIR__ . '/header.php'; ?>

  <section class="py-12 px-5">
    <div class="container mx-auto max-w-2xl">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 md:p-10">

          <!-- Heading -->
          <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-yellow-100 p-3 rounded-full mb-4">
              <i class="fas fa-calendar-check text-yellow-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-800">Konfirmasi Booking</h1>
            <p class="text-gray-500 mt-2">Periksa kembali detail servis Anda sebelum melanjutkan</p>
          </div>

          <!-- Ringkasan data (diisi dari sessionStorage via JS) -->
          <div class="bg-gray-50 rounded-xl p-5 mb-6 space-y-3 text-sm">
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">Nama</span>
              <span class="font-semibold text-gray-800" id="konfNama"></span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">Email</span>
              <span class="font-semibold text-gray-800" id="konfEmail"></span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">No. Telepon</span>
              <span class="font-semibold text-gray-800" id="konfPhone"></span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">Perangkat</span>
              <span class="font-semibold text-gray-800" id="konfPerangkat"></span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">Kerusakan</span>
              <span class="font-semibold text-gray-800" id="konfKerusakan"></span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-2">
              <span class="text-gray-500">Cabang</span>
              <span class="font-semibold text-gray-800" id="konfCabang"></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Deskripsi</span>
              <span class="font-semibold text-gray-800 text-right max-w-xs" id="konfDeskripsi"></span>
            </div>
          </div>

          <!-- Form hidden untuk submit ke PHP -->
          <form id="bookingForm" method="POST" action="/COST-APP/src/api/simpan-booking.php">
            <input type="hidden" name="nama"        id="inputNama">
            <input type="hidden" name="email"       id="inputEmail">
            <input type="hidden" name="no_telepon"  id="inputPhone">
            <input type="hidden" name="perangkat"   id="inputPerangkat">
            <input type="hidden" name="kerusakan"   id="inputKerusakan">
            <input type="hidden" name="cabang"      id="inputCabang">
            <input type="hidden" name="deskripsi"   id="inputDeskripsi">

            <div class="flex flex-col sm:flex-row gap-3">
              <a href="/COST-APP/src/pages/perhitungan.php"
                class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-100 transition text-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
              </a>
              <button type="submit"
                class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition">
                <i class="fas fa-check mr-2"></i>Konfirmasi Booking
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-gray-900 text-gray-300 py-8 mt-12 text-center text-sm">
    <p>© 2024 Geeko Komputer. All rights reserved.</p>
  </footer>

  <script>
    const labelPerangkat = {
      macbook: 'MacBook Pro / Air',
      windows: 'Windows Laptop',
      pc:      'Desktop PC',
      imac:    'iMac / Mac Desktop',
      other:   'Lainnya',
    };
    const labelKerusakan = {
      lcd:     'Layar Pecah / LCD Rusak',
      battery: 'Baterai Kembang / Drop',
      ssd:     'Upgrade SSD',
      thermal: 'Thermal Paste / Cleaning',
      other:   'Lainnya',
    };
    const labelCabang = {
      surabaya: 'Surabaya',
    };

    // Ambil data dari sessionStorage
    const data = JSON.parse(sessionStorage.getItem('dataBooking') || '{}');

    if (!data.device) {
      // Kalau tidak ada data, kembali ke form estimasi
      window.location.href = '/COST-APP/src/pages/perhitungan.php';
    }

    // Tampilkan ringkasan
    document.getElementById('konfNama').textContent      = data.nama      || '-';
    document.getElementById('konfEmail').textContent     = data.email     || '-';
    document.getElementById('konfPhone').textContent     = data.phone     || '-';
    document.getElementById('konfPerangkat').textContent = labelPerangkat[data.device]  || data.device  || '-';
    document.getElementById('konfKerusakan').textContent = labelKerusakan[data.issue]   || data.issue   || '-';
    document.getElementById('konfCabang').textContent    = labelCabang[data.branch]     || data.branch  || '-';
    document.getElementById('konfDeskripsi').textContent = data.description || '-';

    // Isi input hidden untuk form submit
    document.getElementById('inputNama').value      = data.nama        || '';
    document.getElementById('inputEmail').value     = data.email       || '';
    document.getElementById('inputPhone').value     = data.phone       || '';
    document.getElementById('inputPerangkat').value = data.device      || '';
    document.getElementById('inputKerusakan').value = data.issue       || '';
    document.getElementById('inputCabang').value    = data.branch      || '';
    document.getElementById('inputDeskripsi').value = data.description || '';
  </script>

</body>
</html>