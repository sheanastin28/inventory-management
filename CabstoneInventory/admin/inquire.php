<?php
session_start();
include '../lib/db/db.php';

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inq_id']) && isset($_POST['status'])) {
    $inq_id = $_POST['inq_id'];
    $status = $_POST['status'];

    if (in_array($status, ['Pending', 'Accept', 'Decline'])) {
        $stmt = $conn->prepare("UPDATE inquire SET status = ? WHERE inq_id = ?");
        $stmt->bind_param("ss", $status, $inq_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all inquiry data
$sql = "
    SELECT 
        i.*, 
        p.product
    FROM 
        inquire i
    LEFT JOIN 
        product p ON i.product_id = p.product_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Table</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <link rel="stylesheet" href="../lib/css/modal.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">

    <?php include 'header.php';?>
    <h2 style="text-align: center; color: #10162F;">Kitchen Cabinet Inquiries</h2>

    <div class="table-box">
        <table class="table">
            <thead>
                <tr>
                    <!-- <th>Inquire ID</th> -->
                    <th>Name</th>
                    <th>Product</th>
                    <th>View</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['product']) ?></td>
                            <td>
                                <?php
                                    $encodedData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    echo "<button onclick='viewInquiry($encodedData)'>View</button>";
                                ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="inq_id" value="<?= $row['inq_id'] ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Accept" <?= $row['status'] == 'Accept' ? 'selected' : '' ?>>Accept</option>
                                        <option value="Decline" <?= $row['status'] == 'Decline' ? 'selected' : '' ?>>Decline</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No inquiries found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="inquiryModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <h2>Kitchen Cabinet Form</h2>

        <label>Email</label>
        <input type="text" id="email" readonly>

        <label>Name</label>
        <input type="text" id="name" readonly>

        <label>Address</label>
        <input type="text" id="address" readonly>

        <label>Contact Number</label>
        <input type="text" id="cont_no" readonly>

        <label>Preferred Contact Method</label>
        <input type="text" id="com_method" readonly>

        <label>Space Dimensions</label>
        <input type="text" id="length" placeholder="Length" readonly>
        <input type="text" id="width" placeholder="Width" readonly>
        <input type="text" id="height" placeholder="Height" readonly>

        <label>Budget</label>
        <input type="text" id="budget" readonly>

        <label>Additional Queries</label>
        <input type="text" id="query" readonly>
    </div>
</div>

<script src="../lib/js/main.js"></script>

</body>
</html>

<?php
$conn->close();
?>
