<?php
session_start();
include '../lib/db/db.php';

// Handle approve/decline action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inq_id']) && isset($_POST['action'])) {
    $inq_id = $_POST['inq_id'];
    $action = $_POST['action'];

    // Map actions to track_id
    $trackMap = [
        'approve' => 'TRK-0003', // Approved
        'decline' => 'TRK-0002'  // Declined
    ];

    if (isset($trackMap[$action])) {
        $newTrackId = $trackMap[$action];
        $stmt = $conn->prepare("UPDATE inquire SET track_id = ? WHERE inq_id = ?");
        $stmt->bind_param("ss", $newTrackId, $inq_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch inquiries with track description
$sql = "
    SELECT i.*, p.product, t.track
    FROM inquire i
    LEFT JOIN product p ON i.product_id = p.product_id
    LEFT JOIN track t ON i.track_id = t.track_id
    ORDER BY i.date DESC
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
            padding: 12px;
            border-radius: 8px;
            width: 95%;
            max-width: 450px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.2s ease-out;
            max-height: 95vh;
            overflow-y: auto;
        }
        .modal-close {
            float: right;
            font-size: 18px;
            font-weight: bold;
            color: #555;
            cursor: pointer;
        }
        .modal-close:hover {
            color: #000;
        }
        .modal-content h2 {
            font-size: 18px;
            margin-bottom: 8px;
            text-align: center;
            color: #222;
        }
        .modal-content label {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
            display: block;
        }
        .modal-content input[type="text"] {
            width: 96%;
            padding: 5px;
            margin-bottom: 5px;
            font-size: 11px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .dimension-row {
            display: flex;
            gap: 6px;
            margin-bottom: 6px;
        }
        .dimension-group input {
            width: 100%;
            font-size: 11px;
            padding: 4px;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }
        .approve-btn, .decline-btn {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .approve-btn { background: #28a745; }
        .approve-btn:hover { background: #218838; }
        .decline-btn { background: #dc3545; }
        .decline-btn:hover { background: #c82333; }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <?php include 'header.php'; ?>
    <h2 style="text-align:left; color:#10162F; font-size:30px;">Cabinet Inquiries</h2>

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
                                    $encodedData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    echo "<button onclick='viewInquiry($encodedData)'>View</button>";
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['track']) ?></td>
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
        <input type="hidden" id="inq_id" value="">

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

        <div class="action-buttons">
            <form method="POST">
                <input type="hidden" name="inq_id" id="approve_inq_id">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="approve-btn">Approve</button>
            </form>
            <form method="POST">
                <input type="hidden" name="inq_id" id="decline_inq_id">
                <input type="hidden" name="action" value="decline">
                <button type="submit" class="decline-btn">Decline</button>
            </form>
        </div>
    </div>
</div>

<script>
function viewInquiry(data) {
    document.getElementById('productTitle').innerText = (data.product ?? "Product") + " Form";
    document.getElementById('approve_inq_id').value = data.inq_id;
    document.getElementById('decline_inq_id').value = data.inq_id;

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

</body>
</html>

<?php $conn->close(); ?>
