// ======================== LOGIN LOGIC ========================

// Data user simulasi (dalam implementasi nyata, ini dari backend)
const users = [
    {
        id: 1,
        name: "Pelanggan Demo",
        email: "pelanggan@geeko.com",
        password: "123456",
        role: "pelanggan",
        phone: "081234567890"
    },
    {
        id: 2,
        name: "Teknisi Geeko",
        email: "teknisi@geeko.com",
        password: "123456",
        role: "teknisi",
        phone: "089876543210"
    },
    {
        id: 3,
        name: "Admin Geeko",
        email: "admin@geeko.com",
        password: "admin123",
        role: "admin",
        phone: "088888888888"
    }
];

// Fungsi untuk menampilkan toast notifikasi
function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.innerText = message;
    toast.classList.remove('hidden');
    if (isError) {
        toast.classList.add('bg-red-600', 'text-white');
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('opacity-0', 'bg-red-600');
                toast.classList.add('bg-gray-800');
            }, 300);
        }, 3000);
    } else {
        toast.classList.add('bg-green-600', 'text-white');
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('opacity-0', 'bg-green-600');
                toast.classList.add('bg-gray-800');
            }, 300);
        }, 2000);
    }
}

// Validasi form
function validateForm(email, password) {
    let isValid = true;
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    
    // Reset error
    emailError.classList.add('hidden');
    passwordError.classList.add('hidden');
    document.getElementById('email').classList.remove('border-red-500');
    document.getElementById('password').classList.remove('border-red-500');
    
    if (!email) {
        emailError.innerText = 'Email wajib diisi';
        emailError.classList.remove('hidden');
        document.getElementById('email').classList.add('border-red-500');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        emailError.innerText = 'Format email tidak valid';
        emailError.classList.remove('hidden');
        document.getElementById('email').classList.add('border-red-500');
        isValid = false;
    }
    
    if (!password) {
        passwordError.innerText = 'Password wajib diisi';
        passwordError.classList.remove('hidden');
        document.getElementById('password').classList.add('border-red-500');
        isValid = false;
    } else if (password.length < 4) {
        passwordError.innerText = 'Password minimal 4 karakter';
        passwordError.classList.remove('hidden');
        document.getElementById('password').classList.add('border-red-500');
        isValid = false;
    }
    
    return isValid;
}

// Proses login
function processLogin(email, password) {
    // Cari user
    const user = users.find(u => u.email === email && u.password === password);
    
    if (!user) {
        showToast('Email atau password salah!', true);
        return false;
    }
    
    // Simpan session
    sessionStorage.setItem('user_logged_in', 'true');
    sessionStorage.setItem('user_id', user.id);
    sessionStorage.setItem('user_name', user.name);
    sessionStorage.setItem('user_email', user.email);
    sessionStorage.setItem('user_role', user.role);
    
    // Simpan remember me jika dicentang
    if (document.getElementById('remember').checked) {
        localStorage.setItem('remember_email', email);
        localStorage.setItem('remember_password', password);
    } else {
        localStorage.removeItem('remember_email');
        localStorage.removeItem('remember_password');
    }
    
    // Redirect berdasarkan role
    if (user.role === 'teknisi' || user.role === 'admin') {
        showToast(`Selamat datang, ${user.name}! Mengarahkan ke dashboard teknisi...`);
        setTimeout(() => {
            window.location.href = 'dashboard-teknisi.html';
        }, 1000);
    } else {
        showToast(`Selamat datang, ${user.name}! Mengarahkan ke halaman utama...`);
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    }
    return true;
}

// Auto-fill dari localStorage (remember me)
function autoFillRemembered() {
    const rememberedEmail = localStorage.getItem('remember_email');
    const rememberedPassword = localStorage.getItem('remember_password');
    if (rememberedEmail && rememberedPassword) {
        document.getElementById('email').value = rememberedEmail;
        document.getElementById('password').value = rememberedPassword;
        document.getElementById('remember').checked = true;
    }
}

// Toggle password visibility
function setupPasswordToggle() {
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = toggleBtn.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
}

// Event listener untuk form submit
function setupFormSubmit() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        if (!validateForm(email, password)) {
            return;
        }
        
        // Tampilkan loading
        submitBtn.disabled = true;
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `<span class="loader-white"></span> Memproses...`;
        submitBtn.classList.add('btn-loading');
        
        // Simulasi proses login (bisa diganti dengan fetch ke backend)
        setTimeout(() => {
            const success = processLogin(email, password);
            if (!success) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
                submitBtn.classList.remove('btn-loading');
            }
        }, 800);
    });
}

// Cek apakah user sudah login sebelumnya
function checkAlreadyLoggedIn() {
    const isLoggedIn = sessionStorage.getItem('user_logged_in');
    const userRole = sessionStorage.getItem('user_role');
    if (isLoggedIn === 'true') {
        // Redirect jika sudah login
        if (userRole === 'teknisi' || userRole === 'admin') {
            window.location.href = 'dashboard-teknisi.html';
        } else {
            window.location.href = 'index.html';
        }
    }
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', () => {
    checkAlreadyLoggedIn();
    autoFillRemembered();
    setupPasswordToggle();
    setupFormSubmit();
});