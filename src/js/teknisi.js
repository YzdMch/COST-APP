// ======================== CEK SESSION LOGIN ========================
function checkLoginAndRedirect() {
    const isLoggedIn = sessionStorage.getItem('user_logged_in');
    const userRole = sessionStorage.getItem('user_role');
    if (!isLoggedIn || (userRole !== 'teknisi' && userRole !== 'admin')) {
        // Belum login atau bukan teknisi, redirect ke halaman login
        window.location.href = 'login.html';
    }
    // Tampilkan nama teknisi
    const techName = sessionStorage.getItem('user_name') || 'Teknisi';
    document.getElementById('techNameDisplay').innerText = techName;
}

// ======================== DATA SERVI (localStorage) ========================
let services = [];

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
    saveServicesToLocalStorage(); // simpan dummy ke localStorage
}

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
                <button onclick="openUpdateModal('${service.id}')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm transition"><i class="fas fa-edit"></i> Update</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Modal update
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

function saveUpdate() {
    if (!currentServiceId) return;
    const service = services.find(s => s.id === currentServiceId);
    if (!service) return;

    const newStatus = document.getElementById('statusSelect').value;
    const newNote = document.getElementById('noteInput').value;
    const photoFile = document.getElementById('progressPhoto').files[0];

    let photoName = service.photoProgress;
    if (photoFile) {
        photoName = `uploads/${Date.now()}_${photoFile.name}`;
        // Di sini hanya simulasi, tidak benar-benar upload
    }

    service.status = newStatus;
    service.notes = newNote;
    service.photoProgress = photoName;
    service.lastUpdate = new Date().toISOString();

    saveServicesToLocalStorage();
    alert(`✅ Update berhasil!\nStatus: ${newStatus}\nCatatan: ${newNote || '-'}`);
    renderTable();
    closeModal();
}

// Logout
function setupLogout() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            sessionStorage.removeItem('user_logged_in');
            sessionStorage.removeItem('user_role');
            sessionStorage.removeItem('user_name');
            window.location.href = 'login.html';
        });
    }
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', () => {
    checkLoginAndRedirect();
    loadServicesFromLocalStorage();
    renderTable();
    setupLogout();

    // Modal event listeners
    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('cancelModal').addEventListener('click', closeModal);
    document.getElementById('saveUpdate').addEventListener('click', saveUpdate);
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('updateModal');
        if (e.target === modal) closeModal();
    });
});