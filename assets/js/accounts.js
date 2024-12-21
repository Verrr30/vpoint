document.addEventListener("DOMContentLoaded", function () {
  // Preview gambar utama
  const mainImageInput = document.getElementById("main_image");
  const mainImagePreview = document.getElementById("main_image_preview");

  mainImageInput.addEventListener("change", function () {
    previewImage(this, mainImagePreview);
  });

  // Preview gambar tambahan
  const additionalImagesInput = document.querySelector(
    'input[name="additional_images[]"]'
  );
  const additionalImagesPreview = document.getElementById(
    "additional_images_preview"
  );

  additionalImagesInput.addEventListener("change", function () {
    previewMultipleImages(this, additionalImagesPreview);
  });

  // Tambah input gambar tambahan
  const addImageBtn = document.querySelector(".add-image-btn");
  addImageBtn.addEventListener("click", function () {
    const newInput = document.createElement("div");
    newInput.className = "image-upload-row";
    newInput.innerHTML = `
            <input type="file" name="additional_images[]" accept="image/*">
            <button type="button" class="btn btn-sm btn-danger remove-input-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
    document.querySelector(".additional-images").appendChild(newInput);

    // Add event listener untuk preview
    const input = newInput.querySelector("input");
    input.addEventListener("change", function () {
      previewMultipleImages(this, additionalImagesPreview);
    });

    // Add event listener untuk remove button
    const removeBtn = newInput.querySelector(".remove-input-btn");
    removeBtn.addEventListener("click", function () {
      newInput.remove();
    });
  });
});

function previewImage(input, previewElement) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      previewElement.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function previewMultipleImages(input, previewElement) {
  if (input.files) {
    previewElement.innerHTML = "";

    Array.from(input.files).forEach((file) => {
      const reader = new FileReader();

      reader.onload = function (e) {
        const div = document.createElement("div");
        div.className = "image-preview-item";
        div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-image">×</button>
                `;
        previewElement.appendChild(div);

        // Add event listener untuk remove button
        div
          .querySelector(".remove-image")
          .addEventListener("click", function () {
            div.remove();
          });
      };

      reader.readAsDataURL(file);
    });
  }
}
