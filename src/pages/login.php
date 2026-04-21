<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login • Geeko Komputer</title>
  <link rel="stylesheet" href="../../dist/css/style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center px-4">

  <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">

    <!-- Header -->
    <div class="bg-primary-400 py-5 text-center">
      <img src="../../public/images/logo.png" alt="Geeko Komputer" class="h-10 mx-auto mb-2">
      <h1 class="text-xl font-bold text-gray-800">Selamat Datang Kembali</h1>
      <p class="text-gray-700 text-xs">Login ke akun Geeko Anda</p>
    </div>

    <?php if (!empty($error)) : ?>
      <div class="mx-6 mt-4 bg-red-100 border border-red-300 text-red-700 text-sm px-4 py-3 rounded-xl">
        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="p-6 md:p-8">
      <form id="loginForm" method="POST" action="../../src/api/auth.php">

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-2">Email</label>
          <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="nama@example.com"
              autocomplete="email"
              value="<?= htmlspecialchars($old_email ?? '') ?>"
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-400 transition"
            >
          </div>
          <div id="emailError" class="text-red-500 text-sm mt-1 hidden"></div>
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
              placeholder="••••••••"
              autocomplete="current-password"
              class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-400 transition"
            >
            <button
              type="button"
              id="togglePassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div id="passwordError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Remember & Forgot -->
        <div class="flex items-center justify-between mb-6">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-primary-500 border-gray-300 rounded">
            <span class="text-sm text-gray-600">Ingat saya</span>
          </label>
          <a href="#" class="text-sm text-primary-600 hover:text-primary-700 hover:underline">Lupa password?</a>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2"
        >
          <span>Login</span>
          <i class="fas fa-arrow-right"></i>
        </button>

      </form>

      <!-- Register -->
      <div class="mt-6 text-center">
        <p class="text-gray-600">
          Belum punya akun?
          <a href="register.php" class="text-primary-600 font-semibold hover:underline">Daftar sekarang</a>
        </p>
      </div>

      <!-- Demo akun -->
      <div class="mt-6 pt-4 border-t border-gray-200 text-center space-y-1">
        <p class="text-xs text-gray-400">Demo akun:</p>
        <p class="text-xs text-gray-400">Pelanggan: pelanggan@geeko.com / 123456</p>
        <p class="text-xs text-gray-400">Teknisi: teknisi@geeko.com / 123456</p>
      </div>
    </div>
  </div>

  <script>
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

    document.getElementById('loginForm').addEventListener('submit', function (e) {
      let valid = true;
      const email    = document.getElementById('email');
      const password = document.getElementById('password');
      const emailErr = document.getElementById('emailError');
      const passErr  = document.getElementById('passwordError');

      emailErr.classList.add('hidden');
      passErr.classList.add('hidden');

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        emailErr.textContent = 'Format email tidak valid';
        emailErr.classList.remove('hidden');
        valid = false;
      }

      if (password.value.length < 6) {
        passErr.textContent = 'Password minimal 6 karakter';
        passErr.classList.remove('hidden');
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>
</html>