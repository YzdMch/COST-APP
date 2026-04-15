<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun • Geeko Komputer</title>
  <link rel="stylesheet" href="/COST-APP/dist/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center px-4 py-10">

  <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">

    <!-- Header -->
    <div class="bg-yellow-400 py-5 text-center">
      <img src="/COST-APP/public/images/logo.png" alt="Geeko Komputer" class="h-10 mx-auto mb-2">
      <h1 class="text-xl font-bold text-gray-800">Buat Akun Baru</h1>
      <p class="text-gray-700 text-xs">Daftar untuk mulai booking servis</p>
    </div>

    <?php if (!empty($error)) : ?>
      <div class="mx-6 mt-4 bg-red-100 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl">
        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
      <div class="mx-6 mt-4 bg-green-100 border border-green-300 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <div class="p-6 md:p-8">
      <form id="registerForm" method="POST" action="/COST-APP/src/api/register.php">

        <!-- Nama -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
          <div class="relative">
            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="text"
              name="nama"
              id="nama"
              placeholder="Contoh: John Doe"
              value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
            >
          </div>
          <div id="namaError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Email</label>
          <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="john@example.com"
              value="<?= htmlspecialchars($old['email'] ?? '') ?>"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
            >
          </div>
          <div id="emailError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- No Telepon -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
          <div class="relative">
            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="tel"
              name="no_telepon"
              id="no_telepon"
              placeholder="08123456789"
              value="<?= htmlspecialchars($old['no_telepon'] ?? '') ?>"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
            >
          </div>
          <div id="teleponError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Password</label>
          <div class="relative">
            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Minimal 6 karakter"
              class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
            >
            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div id="passwordError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-6">
          <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
          <div class="relative">
            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="password"
              name="konfirmasi_password"
              id="konfirmasi_password"
              placeholder="Ulangi password"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
            >
          </div>
          <div id="konfirmasiError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2"
        >
          <span>Daftar Sekarang</span>
          <i class="fas fa-arrow-right"></i>
        </button>

      </form>

      <!-- Login -->
      <div class="mt-6 text-center">
        <p class="text-gray-600">
          Sudah punya akun?
          <a href="/COST-APP/src/pages/login.php" class="text-yellow-600 font-semibold hover:underline">Login di sini</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    // Toggle password
    document.getElementById('togglePassword').addEventListener('click', function () {
      const input = document.getElementById('password');
      const icon  = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });

    // Validasi client-side
    document.getElementById('registerForm').addEventListener('submit', function (e) {
      let valid = true;

      const nama      = document.getElementById('nama');
      const email     = document.getElementById('email');
      const telepon   = document.getElementById('no_telepon');
      const password  = document.getElementById('password');
      const konfirm   = document.getElementById('konfirmasi_password');

      const namaErr   = document.getElementById('namaError');
      const emailErr  = document.getElementById('emailError');
      const telErr    = document.getElementById('teleponError');
      const passErr   = document.getElementById('passwordError');
      const konfErr   = document.getElementById('konfirmasiError');

      [namaErr, emailErr, telErr, passErr, konfErr].forEach(el => el.classList.add('hidden'));

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

      if (password.value.length < 6) {
        passErr.textContent = 'Password minimal 6 karakter';
        passErr.classList.remove('hidden');
        valid = false;
      }

      if (konfirm.value !== password.value) {
        konfErr.textContent = 'Password tidak cocok';
        konfErr.classList.remove('hidden');
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>
</html>