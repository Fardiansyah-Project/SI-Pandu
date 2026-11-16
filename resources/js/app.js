const nimInput = document.querySelector('[name="nim"]');
nimInput.addEventListener("input", (e) => {
    const digits = e.target.value.replace(/\D/g, "");
    if (digits.length >= 7) {
        const year = Number(`20${digits.slice(5, 7)}`);
        const input = document.getElementById("angkatan");
        if (
            year >= 2000 &&
            year <= new Date().getFullYear() + 1 &&
            !input.value
        )
            input.value = year;
    }
});
const fotoInput = document.getElementById("foto-input"),
    cropTools = document.getElementById("crop-tools"),
    canvas = document.getElementById("foto-canvas"),
    ctx = canvas.getContext("2d"),
    zoom = document.getElementById("foto-zoom"),
    form = canvas.closest("form");
let sourceImage = null,
    scale = 1,
    offsetX = 0,
    offsetY = 0,
    dragStart = null,
    pointerId = null;

function drawCrop() {
    if (!sourceImage) return;
    const base = Math.max(
        canvas.width / sourceImage.width,
        canvas.height / sourceImage.height,
    );
    const width = sourceImage.width * base * scale,
        height = sourceImage.height * base * scale;
    const minX = canvas.width - width,
        minY = canvas.height - height;
    offsetX = Math.min(0, Math.max(minX, offsetX));
    offsetY = Math.min(0, Math.max(minY, offsetY));
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(sourceImage, offsetX, offsetY, width, height);
}
fotoInput.addEventListener("change", () => {
    const file = fotoInput.files[0];
    if (!file) {
        fotoInput.setCustomValidity("");
        return;
    }
    if (!file.type.startsWith("image/")) {
        fotoInput.setCustomValidity("Pilih berkas gambar JPG atau PNG.");
        fotoInput.reportValidity();
        return;
    }
    fotoInput.setCustomValidity("");
    const url = URL.createObjectURL(file);
    const image = new Image();
    image.onload = () => {
        if (sourceImage?.src?.startsWith("blob:"))
            URL.revokeObjectURL(sourceImage.src);
        sourceImage = image;
        scale = 1;
        offsetX =
            (canvas.width -
                image.width *
                    Math.max(
                        canvas.width / image.width,
                        canvas.height / image.height,
                    )) /
            2;
        offsetY =
            (canvas.height -
                image.height *
                    Math.max(
                        canvas.width / image.width,
                        canvas.height / image.height,
                    )) /
            2;
        zoom.value = "1";
        cropTools.classList.remove("hidden");
        drawCrop();
    };
    image.onerror = () => {
        fotoInput.setCustomValidity(
            "Foto tidak dapat dibaca. Silakan pilih file JPG atau PNG lain.",
        );
        fotoInput.reportValidity();
        URL.revokeObjectURL(url);
    };
    image.src = url;
});
zoom.addEventListener("input", () => {
    if (!sourceImage) return;
    const old = scale;
    scale = Number(zoom.value);
    const factor = scale / old;
    offsetX = canvas.width / 2 - (canvas.width / 2 - offsetX) * factor;
    offsetY = canvas.height / 2 - (canvas.height / 2 - offsetY) * factor;
    drawCrop();
});
canvas.addEventListener("pointerdown", (e) => {
    if (!sourceImage) return;
    dragStart = {
        x: e.clientX,
        y: e.clientY,
        offsetX,
        offsetY,
    };
    pointerId = e.pointerId;
    canvas.setPointerCapture(pointerId);
});
canvas.addEventListener("pointermove", (e) => {
    if (!dragStart || e.pointerId !== pointerId) return;
    const rect = canvas.getBoundingClientRect();
    const factor = canvas.width / rect.width;
    offsetX = dragStart.offsetX + (e.clientX - dragStart.x) * factor;
    offsetY = dragStart.offsetY + (e.clientY - dragStart.y) * factor;
    drawCrop();
});

function stopDrag() {
    dragStart = null;
    pointerId = null;
}
canvas.addEventListener("pointerup", stopDrag);
canvas.addEventListener("pointercancel", stopDrag);
document.getElementById("foto-reset").addEventListener("click", () => {
    if (!sourceImage) return;
    scale = 1;
    zoom.value = "1";
    const base = Math.max(
        canvas.width / sourceImage.width,
        canvas.height / sourceImage.height,
    );
    offsetX = (canvas.width - sourceImage.width * base) / 2;
    offsetY = (canvas.height - sourceImage.height * base) / 2;
    drawCrop();
});
form.addEventListener("submit", (event) => {
    if (!sourceImage) {
        if (!fotoInput.files.length) {
            fotoInput.setCustomValidity("");
            return;
        }
        event.preventDefault();
        fotoInput.setCustomValidity(
            "Pilih foto dan tunggu pratinjau crop selesai dimuat.",
        );
        fotoInput.reportValidity();
        return;
    }
    event.preventDefault();
    canvas.toBlob(
        (blob) => {
            if (!blob) {
                fotoInput.setCustomValidity(
                    "Gagal memproses hasil crop. Silakan pilih foto kembali.",
                );
                fotoInput.reportValidity();
                return;
            }
            const cropped = new File([blob], "foto-ktm.jpg", {
                type: "image/jpeg",
                lastModified: Date.now(),
            });
            const transfer = new DataTransfer();
            transfer.items.add(cropped);
            fotoInput.files = transfer.files;
            HTMLFormElement.prototype.submit.call(form);
        },
        "image/jpeg",
        0.92,
    );
});
