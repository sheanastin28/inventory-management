// Realtime clock
function updateDateTime() {
    const now = new Date();
    const options = {
        year: "numeric", month: "long", day: "numeric",
        hour: "numeric", minute: "2-digit", second: "2-digit",
        hour12: true
    };
    document.getElementById("datetime").textContent = now.toLocaleString("en-PH", options);
}
updateDateTime();
setInterval(updateDateTime, 1000);

// Add User Modal
function openModal() {
    document.getElementById("addUserModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("addUserModal").style.display = "none";
}

// Archive Confirmation Modal
function openArchiveModal() {
    document.getElementById("archiveModal").style.display = "flex";
}
function closeArchiveModal() {
    document.getElementById("archiveModal").style.display = "none";
}

// Confirm Archive: close archive modal and show success modal
function confirmArchive() {
    closeArchiveModal();
    openSuccessModal();
}

// Edit User Modal
function openEditModal() {
    document.getElementById("editUserModal").style.display = "flex";
}
function closeEditModal() {
    document.getElementById("editUserModal").style.display = "none";
}

// Confirm Edit: close edit modal and show success modal
function confirmEdit() {
    closeEditModal();
    openEditSuccessModal();
}

function openEditModal(userId, fullname, username, role) {
    document.getElementById('editUserId').value = userId;
    document.getElementById('editFullname').value = fullname;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRole').value = role;
    document.getElementById('editUserModal').style.display = 'block';
}

function openArchiveModal(userId) {
    document.getElementById('archiveUserId').value = userId;
    document.getElementById('archiveModal').style.display = 'block';
}

function showSuccessModal() {
    document.getElementById('successModal').style.display = 'flex';
}

function closeSuccessModal() {
    document.getElementById('successModal').style.display = 'none';
    window.location.href = "user_management.php"; // Refresh without query param
}