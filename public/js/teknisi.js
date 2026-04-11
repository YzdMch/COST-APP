// ======================== DATA SIMULASI SERVI ========================
let services = [];

// Fungsi untuk memuat data dari localStorage (dari halaman perhitungan.html)
function loadServicesFromLocalStorage() {
    const stored = localStorage.getItem('geeko_services');
    if (stored) {
        try {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed) && parsed.length) {
                services = parsed;
                return;
            }
        } catch(e) {}
    }
    // Data dummy jika kosong
    services = [
        {
            id: "GEEK-A3F2D1",
            customer: "Budi Santoso",
            email: "budi@example.com",
            phone: "08123456789",
            device: "Windows Laptop",
            issue: "Layar Pecah",
            branch: "Jakarta Selatan",
            description: "Layar retak sebelah kiri, masih menyala",
            status: "Diterima",
            notes: "",
            photoProgress: "",
            createdAt: new Date().toISOString()
        },
        {
            id: "GEEK-B9C4E2",
            customer: "Siti Aminah",
            email: "siti@example.com",
            phone: "08567891234",
            device: "MacBook Pro",
            issue: "Baterai Kembang",
            branch: "Jakarta Pusat",
            description: "Baterai cepat habis, laptop mati mendadak",
            status: "Sedang dicek",
            notes: "Cek kesehatan baterai",
            photoProgress: "",
            createdAt: new Date().toISOString()
        },
        {
            id: "GEEK-D5H8K1",
            customer: "Reza Maulana",
            email: "reza@example.com",
            phone: "08234567890",
            device: "Desktop PC",
            issue: "Upgrade SSD",
            branch: "Bekasi",
            description: "Tambah SSD 500GB, clone OS",
            status: "Perbaikan",
            notes: "Sedang cloning data",
            photoProgress: "",
            createdAt: new Date().toISOString()
        }
    ];
}

// Simpan ke localStorage
function saveServicesToLocalStorage() {
    localStorage.setItem('geeko_services', JSON.stringify(services));
}

// Render tabel
function renderTable() {
    const tbody = document.getElementById('serviceTableBody');
    if (!tbody) return;
    if (services.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada data servis</td></tr>';
        return;
    }
    tbody.innerHTML = '';
    services.forEach(service => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition';
        let statusClass = '';
        if (service.status === 'Diterima') statusClass = 'status-diterima';
        else if (service.status === 'Sedang dicek') statusClass = 'status-dicek';
        else if (service.status === 'Perbaikan') statusClass = 'status-perbaikan';
        else if (service.status === 'Testing') statusClass = 'status-testing';
        else if (service.status === 'Selesai') statusClass = 'status-selesai';
        
        row.innerHTML = `
            <td class="px-6 py-4 font-mono text-sm">${service.id}</td>
            <td class="px-6 py-4">${service.customer}<br><span class="text-xs text-gray-400">${service.phone}</span></td>
            <td class="px-6 py-4">${service.device}</td>
            <td class="px-6 py-4">${service.issue}</td>
            <td class="px-6 py-4"><span class="status-badge ${statusClass}">${service.status}</span></td>
            <td class="px-6 py-4">
                <button onclick="openUpdateModal('${service.id}')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm"><i class="fas fa-edit"></i> Update</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Variabel global untuk menyimpan id servis yang sedang diupdate
let currentServiceId = null;

window.openUpdateModal = function(serviceId) {
    const service = services.find(s => s.id === serviceId);
    if (!service) return;
    currentServiceId = serviceId;
    document.getElementById('modalTicket').innerText = service.id;
    document.getElementById('modalCustomer').innerHTML = `${service.customer} (${service.phone})`;
    document.getElementById('modalDevice').innerText = `${service.device} - ${service.issue}`;
    document.getElementById('statusSelect').value = service.status;
    document.getElementById('noteInput').value = service.notes || '';
    document.getElementById('progressPhoto').value = '';
    document.getElementById('updateModal').classList.remove('hidden');
};

function closeModal() {
    document.getElementById('updateModal').classList.add('hidden');
    currentServiceId = null;
}

// Simpan update
function saveUpdate() {
    if (!currentServiceId) return;
    const service = services.find(s => s.id === currentServiceId);
    if (!service) return;

    const newStatus = document.getElementById('statusSelect').value;
    const newNote = document.getElementById('noteInput').value;
    const photoFile = document.getElementById('progressPhoto').files[0];

    // Simulasi upload foto (hanya nama file saja untuk demo)
    let photoName = service.photoProgress;
    if (photoFile) {
        photoName = `uploads/${Date.now()}_${photoFile.name}`;
        // Di dunia nyata upload ke server, disini hanya simulasi
    }

    // Update data
    service.status = newStatus;
    service.notes = newNote;
    service.photoProgress = photoName;
    service.lastUpdate = new Date().toISOString();

    // Simpan ke localStorage
    saveServicesToLocalStorage();

    // Tampilkan notifikasi sukses sederhana
    alert(`✅ Update berhasil!\nStatus: ${newStatus}\nCatatan: ${newNote || '-'}`);

    // Render ulang tabel
    renderTable();
    closeModal();
}

// ======================== LOGIN SIMULASI ========================
function checkLogin() {
    const isLoggedIn = sessionStorage.getItem('teknisi_logged_in');
    if (isLoggedIn === 'true') {
        document.getElementById('loginSection').classList.add('hidden');
        document.getElementById('dashboardSection').classList.remove('hidden');
        // Load data
        loadServicesFromLocalStorage();
        saveServicesToLocalStorage();
        renderTable();
        document.getElementById('techNameDisplay').innerText = sessionStorage.getItem('teknisi_name') || 'Admin';
    } else {
        document.getElementById('loginSection').classList.remove('hidden');
        document.getElementById('dashboardSection').classList.add('hidden');
    }
}

// Event listeners setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const logoutBtn = document.getElementById('logoutBtn');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModal');
    const saveUpdateBtn = document.getElementById('saveUpdate');

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('techEmail').value;
        const password = document.getElementById('techPassword').value;
        // Validasi sederhana (hardcode untuk demo)
        if (email === 'admin@geeko.com' && password === 'password123') {
            sessionStorage.setItem('teknisi_logged_in', 'true');
            sessionStorage.setItem('teknisi_name', 'Admin Geeko');
            checkLogin();
        } else {
            alert('Email atau password salah! (Gunakan admin@geeko.com / password123)');
        }
    });

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            sessionStorage.removeItem('teknisi_logged_in');
            sessionStorage.removeItem('teknisi_name');
            checkLogin();
        });
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
    if (saveUpdateBtn) saveUpdateBtn.addEventListener('click', saveUpdate);

    // Tutup modal jika klik di luar
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('updateModal');
        if (e.target === modal) closeModal();
    });

    checkLogin();
});