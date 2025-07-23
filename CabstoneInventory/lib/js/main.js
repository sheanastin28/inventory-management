function updateDateTime() {
    const now = new Date();

    const options = {
        weekday: undefined,
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
        second: "2-digit",
        hour12: true
    };

    const formatted = now.toLocaleString("en-PH", options);
    document.getElementById("datetime").textContent = formatted;
}

// Update immediately and every second
updateDateTime();
setInterval(updateDateTime, 1000);

document.addEventListener("DOMContentLoaded", function () {
    const userToggle = document.getElementById("userToggle");
    const userMenu = document.getElementById("userMenu");

    if (userToggle && userMenu) {
        userToggle.addEventListener("click", function () {
        userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", function (e) {
        if (!userToggle.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.style.display = "none";
        }
        });
    }
});

function previewAddImage(event) {
    const img = document.getElementById('addImagePreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}

function previewEditImage(event) {
  const img = document.getElementById('editImagePreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}

function openEditModal(id, name, description) {
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_description').value = description;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function openEditImageModal(id) {
    document.getElementById('edit_img_id').value = id;
    new bootstrap.Modal(document.getElementById('editImageModal')).show();
}

function toggleAddRaw() {
    var form = document.getElementById("addRawForm");
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
}

function viewInquiry(data) {
    console.log(data); // Debugging
    document.getElementById('email').value = data.email || '';
    document.getElementById('name').value = data.name || '';
    document.getElementById('address').value = data.address || '';
    document.getElementById('cont_no').value = data.cont_no || '';
    document.getElementById('com_method').value = data.com_method || '';
    document.getElementById('length').value = data.length || '';
    document.getElementById('width').value = data.width || '';
    document.getElementById('height').value = data.height || '';
    document.getElementById('budget').value = data.budget || '';
    document.getElementById('query').value = data.query || '';

    document.getElementById('inquiryModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('inquiryModal').style.display = 'none';
}

window.onclick = function(e) {
    if (e.target === document.getElementById('inquiryModal')) {
        closeModal();
    }
}
