<?php
session_start();
include '../lib/db/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="/lib/css/style.css">
    <link rel="stylesheet" href="/lib/css/modal.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

<?php 
include 'header.php';

// Fetch users from database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <script>
        window.onload = function() {
            showSuccessModal();
        };
    </script>
<?php endif; ?>

    <h1>User Management</h1>

    <div class="user-table-box">
        <h3><i class="fas fa-table-cells-large"></i> USERS
            <button class="add-user-btn" onclick="openModal()">
                <i class="fas fa-plus"></i> Add New
            </button>
        </h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>User Role</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['last_login']) ?></td>
                        <td>
                            <i class="fas fa-edit action-icon"
                                onclick="openEditModal(
                                    '<?= $row['user_id'] ?>',
                                    '<?= htmlspecialchars(addslashes($row['fullname'])) ?>',
                                    '<?= htmlspecialchars(addslashes($row['username'])) ?>',
                                    '<?= $row['role'] ?>'
                                )"></i>

                            <i class="fas fa-trash action-icon"
                                onclick="openArchiveModal('<?= $row['user_id'] ?>')"></i>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No users found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>


<!-- Add User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <h2>Add New User</h2>
        <form action="user_action.php" method="POST">
            <label>Full Name:</label>
            <input type="text" name="fullname" placeholder="Enter full name" required>

            <label>Username:</label>
            <input type="text" name="username" placeholder="Enter username" required>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="">Select role</option>
                <option value="Admin">Admin</option>
                <option value="Employee">Employee</option>
                <option value="Owner">Owner</option>
            </select>

            <div class="modal-buttons">
                <button type="submit" name="add_user" class="add-btn">Add</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
    <div class="modal-content">
        <h2>Edit User</h2>
        <form action="user_action.php" method="POST">
            <input type="hidden" name="user_id" id="editUserId">

            <label>Full Name:</label>
            <input type="text" name="fullname" id="editFullname" required>

            <label>Username:</label>
            <input type="text" name="username" id="editUsername" required>

            <label>Role:</label>
            <select name="role" id="editRole" required>
                <option value="">Select role</option>
                <option value="Admin">Admin</option>
                <option value="Owner">Owner</option>
                <option value="Employee">Employee</option>
            </select>

            <div class="modal-buttons">
                <button type="submit" name="edit_user" class="add-btn">Edit</button>
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal" id="archiveModal">
    <form action="user_action.php" method="POST">
        <input type="hidden" name="user_id" id="archiveUserId">
        <div class="confirmation-box">
            <p>Are you sure you want to archive this user?</p>
            <div class="confirmation-buttons">
                <button type="submit" name="archive_user" class="yes-btn">Yes</button>
                <button type="button" class="no-btn" onclick="closeArchiveModal()">No</button>
            </div>
        </div>
    </form>
</div>

<!-- Success Modal -->
<div class="modal" id="successModal">
    <div class="success-box">
        <i class="fas fa-check-circle success-icon"></i>
        <h2>Successful!</h2>
        <button class="ok-btn" onclick="closeSuccessModal()">OK</button>
    </div>
</div>

<script src="../lib/js/modal.js"></script>

</body>
</html>
