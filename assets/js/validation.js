document
  .getElementById("payment_proof")
  .addEventListener("change", function (e) {
    const file = e.target.files[0];
    const fileSize = file.size / 1024 / 1024; // convert to MB
    const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

    if (fileSize > 2) {
      alert("Ukuran file terlalu besar. Maksimal 2MB");
      this.value = "";
      return;
    }

    if (!allowedTypes.includes(file.type)) {
      alert("Format file tidak didukung. Gunakan JPG, PNG, atau JPEG");
      this.value = "";
      return;
    }
  });
