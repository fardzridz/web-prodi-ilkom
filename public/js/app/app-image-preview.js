(function () {
    document.addEventListener("change", (event) => {
        const input = event.target;
        if (!input.matches("[data-image-input]")) return;
        if (!input.files?.length) return;
        const [file] = input.files;

        const upload = input.closest("[data-image-upload]");
        const preview = upload?.querySelector("[data-image-preview]");
        const fileName = input.closest(".activity-field")?.querySelector("[data-image-file-name]");
        const overlayLabel = upload?.querySelector("[data-image-overlay-label]");

        if (fileName) fileName.textContent = file.name;

        if (file.size > 2_097_152) {
            const existing = document.querySelector(".activity-field-error[id^='activity-image']");
            if (!existing) {
                const error = document.createElement("small");
                error.className = "activity-field-error";
                error.id = "activity-image-error";
                error.textContent = "Ukuran gambar maksimal 2 MB.";
                input.insertAdjacentElement("afterend", error);
            }
            input.value = "";
            return;
        }

        if (preview && file.type.startsWith("image/")) {
            const objectUrl = URL.createObjectURL(file);
            const existingImg = preview.querySelector("img");
            if (existingImg) {
                URL.revokeObjectURL(existingImg.src);
                existingImg.remove();
            }
            const img = document.createElement("img");
            img.src = objectUrl;
            img.alt = "Pratinjau gambar terpilih";
            img.dataset.objectUrl = objectUrl;
            preview.appendChild(img);
            preview.removeAttribute("data-empty");
            if (overlayLabel) overlayLabel.textContent = "Ganti gambar";
        }
    });
})();
