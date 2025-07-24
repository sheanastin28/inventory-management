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

// ✅ Keep original field name: date
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
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
            margin-top: 50px;
        }
        .modal-content {
            background-color: #fff;
            margin: 2% auto;
            padding: 10px 15px;
            border-radius: 8px;
            width: 95%;
            max-width: 480px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.2s ease-out;
            max-height: 96vh;
            overflow-y: auto;
        }
        .modal-close {
            float: right;
            font-size: 20px;
            font-weight: bold;
            color: #555;
            cursor: pointer;
        }
        .modal-close:hover {
            color: #000;
        }
        .modal-content input[type="text"] {
            width: 95%;
            padding: 6px 8px;
            margin-bottom: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 12px;
        }
        .modal-content label {
            font-weight: 700;
            font-size: 12px;
            display: block;
            margin-top: 6px;
            margin-bottom: 2px;
            color: #333;
        }
        .modal-content h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #222;
        }
        .dimension-row {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
        }
        .dimension-group {
            flex: 1;
        }
        .dimension-group label {
            display: block;
            font-size: 12px;
            margin-bottom: 2px;
            color: #333;
        }
        .dimension-group input[type="text"] {
            width: 85%;
            padding: 6px 8px;
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <?php include 'header.php';?>
    <h2 style="text-align: left; color: #10162F; font-size: 35px;">Cabinet Inquiries</h2>

    <div class="table-box">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Product</th>
                    <th>Date</th>
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
                            <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
                            <td>
                                <?php
                                    $encodedData = json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
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
        <h2 id="productTitle">Product Form</h2>

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

        <label><b>Estimated Space Dimensions</b></label>
        <div class="dimension-row">
            <div class="dimension-group">
                <label>Length</label>
                <input type="text" id="length" readonly>
            </div>
            <div class="dimension-group">
                <label>Width</label>
                <input type="text" id="width" readonly>
            </div>
            <div class="dimension-group">
                <label>Height</label>
                <input type="text" id="height" readonly>
            </div>
        </div>

        <label>Budget</label>
        <input type="text" id="budget" readonly>

        <label>Additional Queries</label>
        <input type="text" id="query" readonly>
    </div>
</div>

<script>
function viewInquiry(data) {
    document.getElementById('productTitle').innerText = (data.product ?? "Product") + " Form";

    // ✅ Use the original key: date
    document.getElementById('inquiry_date').value = data.date || '';

    document.getElementById('email').value = data.email;
    document.getElementById('name').value = data.name;
    document.getElementById('address').value = data.address;
    document.getElementById('cont_no').value = data.cont_no;
    document.getElementById('com_method').value = data.com_method;
    document.getElementById('length').value = data.length;
    document.getElementById('width').value = data.width;
    document.getElementById('height').value = data.height;
    document.getElementById('budget').value = data.budget;
    document.getElementById('query').value = data.query;

    document.getElementById('inquiryModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('inquiryModal').style.display = 'none';
}
</script>

<script src="../lib/js/main.js"></script>

</body>
</html>

<?php $conn->close(); ?>
