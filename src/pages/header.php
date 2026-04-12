<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sudahLogin   = isset($_SESSION['user_id']);
$role         = $_SESSION['role'] ?? null;
$namaUser     = $_SESSION['nama'] ?? null;
$dashboardUrl = $role === 'teknisi'
    ? '/COST-APP/src/pages/dashboard-teknisi.php'
    : '/COST-APP/src/pages/dashboard-pelanggan.php';
?>

<header class="bg-white shadow sticky top-0 z-30">
  <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-4">

    <!-- Logo -->
    <a href="/COST-APP/src/pages/index.php">
      <img src="/COST-APP/public/images/logo.png" class="h-10" alt="Geeko Komputer">
    </a>

    <!-- Nav desktop -->
    <nav class="hidden md:block">
      <ul class="flex gap-6 font-medium text-gray-700">
        <li><a href="/COST-APP/src/pages/index.php"            class="hover:text-yellow-500 transition">Home</a></li>
        <li><a href="/COST-APP/src/pages/index.php#services"   class="hover:text-yellow-500 transition">Layanan</a></li>
        <li><a href="/COST-APP/src/pages/index.php#why"        class="hover:text-yellow-500 transition">Mengapa Kami</a></li>
        <li><a href="/COST-APP/src/pages/index.php#developer"  class="hover:text-yellow-500 transition">Developer</a></li>
        <li><a href="/COST-APP/src/pages/index.php#contact"    class="hover:text-yellow-500 transition">Kontak</a></li>
      </ul>
    </nav>

    <!-- Auth buttons -->
    <div class="flex items-center gap-3 flex-wrap">
      <?php if ($sudahLogin) : ?>
        <span class="text-sm text-gray-600">
          <i class="fas fa-user text-yellow-500 mr-1"></i>
          <?= htmlspecialchars($namaUser) ?>
        </span>
        <a href="<?= $dashboardUrl ?>"
          class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
          <i class="fas fa-th-large mr-1"></i> Dashboard
        </a>
        <a href="/COST-APP/src/api/logout.php"
          class="border border-gray-300 text-gray-600 hover:bg-gray-100 text-sm font-semibold px-4 py-2 rounded-lg transition">
          Logout
        </a>
      <?php else : ?>
        <a href="/COST-APP/src/pages/login.php"
          class="border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm font-semibold px-4 py-2 rounded-lg transition">
          Login
        </a>
        <a href="/COST-APP/src/pages/register.php"
          class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
          Daftar
        </a>
      <?php endif; ?>
    </div>

    <!-- Burger -->
    <button id="burger" class="md:hidden text-2xl text-gray-700">
      <i class="fas fa-bars"></i>
    </button>

  </div>

  <!-- Mobile menu -->
  <div id="mobileNav" class="hidden md:hidden border-t border-gray-100 px-4 py-4">
    <ul class="flex flex-col gap-3 text-gray-700 font-medium mb-4">
      <li><a href="/COST-APP/src/pages/index.php"           class="block hover:text-yellow-500 transition">Home</a></li>
      <li><a href="/COST-APP/src/pages/index.php#services"  class="block hover:text-yellow-500 transition">Layanan</a></li>
      <li><a href="/COST-APP/src/pages/index.php#why"       class="block hover:text-yellow-500 transition">Mengapa Kami</a></li>
      <li><a href="/COST-APP/src/pages/index.php#developer" class="block hover:text-yellow-500 transition">Developer</a></li>
      <li><a href="/COST-APP/src/pages/index.php#contact"   class="block hover:text-yellow-500 transition">Kontak</a></li>
    </ul>
    <div class="flex flex-col gap-2 border-t border-gray-100 pt-3">
      <?php if ($sudahLogin) : ?>
        <a href="<?= $dashboardUrl ?>"
          class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg text-center transition">
          <i class="fas fa-th-large mr-1"></i> Dashboard
        </a>
        <a href="/COST-APP/src/api/logout.php"
          class="border border-gray-300 text-gray-600 text-sm font-semibold px-4 py-2 rounded-lg text-center transition">
          Logout
        </a>
      <?php else : ?>
        <a href="/COST-APP/src/pages/login.php"
          class="border border-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg text-center transition">
          Login
        </a>
        <a href="/COST-APP/src/pages/register.php"
          class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-lg text-center transition">
          Daftar
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Burger script — dipakai di semua halaman -->
<script>
  document.getElementById('burger').addEventListener('click', function () {
    document.getElementById('mobileNav').classList.toggle('hidden');
  });
</script>