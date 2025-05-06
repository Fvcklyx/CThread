// Ganti nav bar
document.getElementById("barsButt").addEventListener("click", function () {
  const visible = document.getElementById("visible");
  const hide = document.getElementById("hide");

  if (visible.style.display !== "none") {
    visible.style.display = "none";
    hide.style.display = "flex";
  } else {
    visible.style.display = "flex";
    hide.style.display = "none";
  }
});

// Set kondisi awal
document.getElementById("hide").style.display = "none";

document.getElementById("editButton").addEventListener("click", function () {
  window.location.href = "../Profile/edit.php";
});

function validateForm() {
  const usn = document.getElementById("usn").value.trim();
  const pw1 = document.getElementById("pw1").value;
  const pw2 = document.getElementById("pw2").value;

  if (usn.length > 10) {
    alert("Username Maksimal 10 Karakter!");
    return false;
  }

  if (pw1.length > 8) {
    alert("Password Maksimal 8 Karakter!");
    return false;
  }

  if (pw1 !== pw2) {
    alert("Password dan Verifikasi Tidak Cocok!");
    return false;
  }

  return true;
}

// Toggle Untuk Baru (Thread)
const modalT = document.getElementById("modalThread");
const newButtonT = document.getElementById("newButton"); // Tombol Baru +
const backButtonT = document.getElementById("backModal"); // Tombol Batal
// Tampilkan modal saat klik Baru +
newButtonT.addEventListener("click", () => {
  modalT.classList.remove("hidden");
});
// Sembunyikan modal saat klik Batal
backButtonT.addEventListener("click", () => {
  modalT.classList.add("hidden");
});

// Modal Edit Thread
document.querySelectorAll(".editThread").forEach((button) => {
  button.addEventListener("click", function () {
    const modalEdit = document.getElementById("editThread");
    modalEdit.classList.remove("hidden");

    // Isi form edit
    modalEdit.querySelector('input[name="titleT"]').value = this.dataset.title;
    modalEdit.querySelector('textarea[name="coreT"]').value = this.dataset.core;

    // Hapus input tersembunyi sebelumnya (jika ada)
    const oldInput = modalEdit.querySelector('input[name="thread_id"]');
    if (oldInput) oldInput.remove();

    // Tambah input hidden yang baru
    modalEdit
      .querySelector("form")
      .insertAdjacentHTML(
        "beforeend",
        `<input type="hidden" name="thread_id" value="${this.dataset.id}">`
      );
  });
});

// Tutup Modal Edit
document.getElementById("backEdit").addEventListener("click", function () {
  document.getElementById("editThread").classList.add("hidden");
  const hiddenInput = document.querySelector(
    '#editThread input[name="thread_id"]'
  );
  if (hiddenInput) hiddenInput.remove();
});
