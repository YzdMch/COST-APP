// Data estimasi harga (simulasi)
const priceMatrix = {
  macbook: { lcd: [800000, 1200000], battery: [450000, 600000], ssd: [350000, 500000], thermal: [150000, 200000], other: [200000, 500000] },
  windows: { lcd: [500000, 800000], battery: [300000, 450000], ssd: [250000, 400000], thermal: [100000, 150000], other: [150000, 400000] },
  pc: { lcd: [600000, 1000000], battery: [400000, 550000], ssd: [300000, 450000], thermal: [120000, 180000], other: [180000, 450000] },
  imac: { lcd: [1500000, 2500000], battery: [800000, 1200000], ssd: [500000, 800000], thermal: [250000, 350000], other: [300000, 700000] },
  other: { lcd: [400000, 700000], battery: [250000, 400000], ssd: [200000, 350000], thermal: [100000, 150000], other: [100000, 300000] },
};

const branchNames = {
  jaksel: "Jakarta Selatan (Head Office)", jakpus: "Jakarta Pusat", jakbar: "Jakarta Barat", jaktim: "Jakarta Timur",
  jakut: "Jakarta Utara", tangerang: "Tangerang", bekasi: "Bekasi", depok: "Depok",
};
const deviceNames = { macbook: "MacBook Pro / Air", windows: "Windows Laptop", pc: "Desktop PC", imac: "iMac / Mac Desktop", other: "Lainnya" };
const issueNames = { lcd: "Layar Pecah / LCD Rusak", battery: "Baterai Kembang / Drop", ssd: "Upgrade SSD", thermal: "Thermal Paste / Cleaning", other: "Lainnya" };

const form = document.getElementById("priceForm");
const submitBtn = document.getElementById("submitBtn");
const resultCard = document.getElementById("resultCard");
const ticketSpan = document.getElementById("ticketNumber");
const resultDevice = document.getElementById("resultDevice");
const resultIssue = document.getElementById("resultIssue");
const resultBranch = document.getElementById("resultBranch");
const resultPriceSpan = document.getElementById("resultPrice");

function formatRupiah(amount) {
  return "Rp " + amount.toFixed(0).replace(/\d(?=(\d{3})+$)/g, "$&.");
}
function getPriceRange(device, issue) {
  if (priceMatrix[device] && priceMatrix[device][issue]) return priceMatrix[device][issue];
  return [0, 0];
}
function generateTicket() {
  return "GEEK-" + Math.random().toString(36).substring(2, 10).toUpperCase();
}

function clearFieldError(groupId) {
  const group = document.getElementById(groupId);
  if (!group) return;
  const input = group.querySelector("input, select, textarea");
  const errorDiv = group.querySelector(".error-msg");
  if (input) input.classList.remove("border-red-500", "border-2");
  if (errorDiv) errorDiv.classList.add("hidden");
}
function showFieldError(groupId, message) {
  const group = document.getElementById(groupId);
  if (!group) return;
  const input = group.querySelector("input, select, textarea");
  const errorDiv = group.querySelector(".error-msg");
  if (input) {
    input.classList.add("border-red-500", "border-2");
    input.classList.remove("border-gray-300");
  }
  if (errorDiv) {
    errorDiv.textContent = message;
    errorDiv.classList.remove("hidden");
  }
}
function validateFieldById(fieldId, groupId, customValidator = null) {
  const field = document.getElementById(fieldId);
  if (!field) return true;
  const value = field.value.trim();
  let isValid = false, errorMsg = "";
  if (customValidator) {
    const result = customValidator(value);
    isValid = result.valid;
    errorMsg = result.msg;
  } else {
    if (field.tagName === "SELECT") {
      isValid = value !== "";
      errorMsg = "Harap pilih opsi";
    } else if (field.type === "email") {
      isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
      errorMsg = "Email tidak valid";
    } else if (field.id === "phone") {
      const clean = value.replace(/[-\s]/g, "");
      isValid = clean.length >= 8 && /^[0-9]+$/.test(clean);
      errorMsg = "Nomor telepon minimal 8 digit angka";
    } else {
      isValid = value !== "";
      errorMsg = "Field ini wajib diisi";
    }
  }
  if (isValid) clearFieldError(groupId);
  else showFieldError(groupId, errorMsg);
  return isValid;
}
function validateDescription() {
  const desc = document.getElementById("description").value.trim();
  if (desc === "") {
    showFieldError("group-description", "Deskripsi harus diisi");
    return false;
  } else {
    clearFieldError("group-description");
    return true;
  }
}
function validateAllFields() {
  return validateFieldById("nama", "group-nama") &&
    validateFieldById("email", "group-email") &&
    validateFieldById("phone", "group-phone", (val) => {
      const clean = val.replace(/[-\s]/g, "");
      const ok = clean.length >= 8 && /^[0-9]+$/.test(clean);
      return { valid: ok, msg: "Nomor telepon minimal 8 digit angka" };
    }) &&
    validateFieldById("device", "group-device") &&
    validateFieldById("issue", "group-issue") &&
    validateFieldById("branch", "group-branch") &&
    validateDescription();
}

// Event listeners real-time
document.getElementById("nama")?.addEventListener("blur", () => validateFieldById("nama", "group-nama"));
document.getElementById("email")?.addEventListener("blur", () => validateFieldById("email", "group-email"));
document.getElementById("phone")?.addEventListener("blur", () => validateFieldById("phone", "group-phone", (val) => {
  const clean = val.replace(/[-\s]/g, "");
  const ok = clean.length >= 8 && /^[0-9]+$/.test(clean);
  return { valid: ok, msg: "Nomor telepon minimal 8 digit angka" };
}));
document.getElementById("device")?.addEventListener("change", () => validateFieldById("device", "group-device"));
document.getElementById("issue")?.addEventListener("change", () => validateFieldById("issue", "group-issue"));
document.getElementById("branch")?.addEventListener("change", () => validateFieldById("branch", "group-branch"));
document.getElementById("description")?.addEventListener("blur", validateDescription);

window.resetForm = function () {
  form.reset();
  resultCard.classList.add("hidden");
  ["group-nama", "group-email", "group-phone", "group-device", "group-issue", "group-branch", "group-description"].forEach(g => {
    clearFieldError(g);
    const groupDiv = document.getElementById(g);
    if (groupDiv) {
      const inp = groupDiv.querySelector("input, select, textarea");
      if (inp) inp.classList.remove("border-red-500", "border-2");
    }
  });
  submitBtn.disabled = false;
  submitBtn.innerHTML = `<span>Dapatkan Estimasi</span> <i class="fas fa-arrow-right"></i>`;
};

form.addEventListener("submit", async function (e) {
  e.preventDefault();
  if (!validateAllFields()) return;
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<span class="loader mr-2"></span> Memproses...`;
  setTimeout(() => {
    const device = document.getElementById("device").value;
    const issue = document.getElementById("issue").value;
    const branch = document.getElementById("branch").value;
    const ticket = generateTicket();
    const [minPrice, maxPrice] = getPriceRange(device, issue);
    let priceText = (minPrice === 0 && maxPrice === 0) ? "Estimasi akan diinformasikan setelah teknisi memeriksa" : `${formatRupiah(minPrice)} - ${formatRupiah(maxPrice)}`;
    ticketSpan.textContent = ticket;
    resultDevice.textContent = deviceNames[device] || device;
    resultIssue.textContent = issueNames[issue] || issue;
    resultBranch.textContent = branchNames[branch] || branch;
    resultPriceSpan.innerHTML = priceText;
    resultCard.classList.remove("hidden");
    resultCard.scrollIntoView({ behavior: "smooth", block: "center" });
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<span>Dapatkan Estimasi</span> <i class="fas fa-arrow-right"></i>`;
    console.log({ nama: document.getElementById("nama").value, email: document.getElementById("email").value, phone: document.getElementById("phone").value, device, issue, branch, description: document.getElementById("description").value, ticket });
  }, 1200);
});

// Burger menu
const burger = document.getElementById("burger");
const mobileNav = document.getElementById("mobileNav");
if (burger && mobileNav) {
  burger.addEventListener("click", () => {
    mobileNav.classList.toggle("hidden");
    const icon = burger.querySelector("i");
    if (!mobileNav.classList.contains("hidden")) {
      icon.classList.remove("fa-bars");
      icon.classList.add("fa-times");
    } else {
      icon.classList.remove("fa-times");
      icon.classList.add("fa-bars");
    }
  });
  mobileNav.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
      mobileNav.classList.add("hidden");
      const icon = burger.querySelector("i");
      icon.classList.remove("fa-times");
      icon.classList.add("fa-bars");
    });
  });
}
resultCard.classList.add("hidden");