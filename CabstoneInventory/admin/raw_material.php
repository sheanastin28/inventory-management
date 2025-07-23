<?php
session_start();
include '../lib/db/db.php';

// Add Category
if (isset($_POST['add'])) {
    $category_id   = uniqid('cat_');
    $category_name = $_POST['category_name'];
    $stmt = $conn->prepare("INSERT INTO category (category_id, category_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_id, $category_name);
    $stmt->execute();
    $stmt->close();
    header("Location: raw_material.php");
    exit;
}

// Update Category
if (isset($_POST['update'])) {
    $id   = $_POST['category_id'];
    $name = $_POST['category_name'];
    $conn->query("UPDATE category SET category_name = '$name' WHERE category_id = '$id'");
    header("Location: raw_material.php");
    exit;
}

// Edit Category
$edit_category = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM category WHERE category_id = '$id'");
    $edit_category = $result->fetch_assoc();
}

// View Raw Materials
$view_data = [];
if (isset($_GET['view'])) {
    $cat = $_GET['view'];
    $stmt = $conn->prepare("SELECT material_id, material, quantity, price FROM raw_material WHERE category_id = ?");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $view_data[] = $row;
    }
    $stmt->close();
}

// Delete Raw Material
if (isset($_GET['delete_raw'])) {
    $raw = $_GET['delete_raw'];
    $cat = $_GET['category'];
    $conn->query("DELETE FROM raw_material WHERE material_id = '$raw'");
    header("Location: raw_material.php?view=$cat");
    exit;
}

// Edit Raw Material
$edit_raw = null;
if (isset($_GET['edit_raw'])) {
    $raw = $_GET['edit_raw'];
    $res = $conn->query("SELECT * FROM raw_material WHERE material_id = '$raw'");
    $edit_raw = $res->fetch_assoc();
}

// Update Raw Material
if (isset($_POST['update_raw'])) {
    $id        = $_POST['material_id'];
    $material  = $_POST['material'];
    $quantity  = $_POST['quantity'];
    $price     = $_POST['price'];
    $cat       = $_POST['category_id'];

    $stmt = $conn->prepare("UPDATE raw_material SET material = ?, quantity = ?, price = ? WHERE material_id = ?");
    $stmt->bind_param("sids", $material, $quantity, $price, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: raw_material.php?view=$cat");
    exit;
}

// Add Raw Material
if (isset($_POST['add_raw'])) {
    $id        = uniqid('mat_');
    $material  = $_POST['material'];
    $quantity  = $_POST['quantity'];
    $price     = $_POST['price'];
    $cat_id    = $_POST['category_id'];

    $stmt = $conn->prepare("INSERT INTO raw_material (material_id, material, quantity, price, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $id, $material, $quantity, $price, $cat_id);
    $stmt->execute();
    $stmt->close();

    header("Location: raw_material.php?view=$cat_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Raw Material Manager</title>
    <link rel="stylesheet" href="../lib/css/style.css">
</head>
<body>
<?php include 'sidebar.php';?>
<div class="main-content">
    <?php include 'header.php';?>
    <div class="container-flex">
        <div class="box">
            <h3><?= $edit_category ? "Edit" : "Add New" ?> Category</h3>
            <form method="POST">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="category_id" value="<?= $edit_category['category_id'] ?>">
                <?php endif; ?>
                <input type="text" name="category_name" placeholder="Category Name" required
                    value="<?= $edit_category['category_name'] ?? '' ?>">
                <button type="submit" name="<?= $edit_category ? 'update' : 'add' ?>">
                    <?= $edit_category ? 'Update' : 'Add' ?> Category
                </button>
            </form>
        </div>

        <div class="box">
            <h3>All Categories</h3>
            <table>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                <?php
                $res = $conn->query("SELECT * FROM category");
                $i   = 1;
                while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['category_name'] ?></td>
                    <td class="actions">
                        <a href="?view=<?= $row['category_id'] ?>"><button class="btn-view">👁 View</button></a>
                        <a href="?edit=<?= $row['category_id'] ?>"><button class="btn-edit">✏ Edit</button></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <?php if (!empty($view_data) || isset($_GET['edit_raw'])): ?>
        <div class="modal<?= (!empty($view_data) || isset($_GET['edit_raw'])) ? ' active' : '' ?>">
            <div class="modal-content">
                <span class="modal-close" onclick="window.location.href='raw_material.php'">&times;</span>
                <h3>Raw Materials in 
                    <?php
                    $cat_result = $conn->query("SELECT category_name FROM category WHERE category_id = '" . $_GET['view'] . "'");
                    echo ($cat_row = $cat_result->fetch_assoc()) ? htmlspecialchars($cat_row['category_name']) : "Unknown";
                    ?>
                </h3>

                <button class="add-raw-btn" onclick="toggleAddRaw()">➕ Add Raw Material</button>

                <div class="popup-form" id="addRawForm">
                    <form method="POST">
                        <input type="hidden" name="category_id" value="<?= $_GET['view'] ?>">
                        <input type="text" name="material" placeholder="Material Name" required>
                        <input type="number" name="quantity" placeholder="Quantity" required>
                        <input type="number" step="0.01" name="price" placeholder="Price" required>
                        <button type="submit" name="add_raw">Add</button>
                    </form>
                </div>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    if (isset($edit_raw)):
                    ?>
                    <form method="POST">
                        <tr>
                            <input type="hidden" name="material_id" value="<?= $edit_raw['material_id'] ?>">
                            <input type="hidden" name="category_id" value="<?= $_GET['view'] ?>">
                            <td><?= $edit_raw['material_id'] ?></td>
                            <td><input type="text" name="material" value="<?= $edit_raw['material'] ?>"></td>
                            <td><input type="number" name="quantity" value="<?= $edit_raw['quantity'] ?>"></td>
                            <td><input type="number" step="0.01" name="price" value="<?= $edit_raw['price'] ?>"></td>
                            <td><button type="submit" name="update_raw" class="btn-edit">💾 Save</button></td>
                        </tr>
                    </form>
                    <?php endif;
                    foreach ($view_data as $item):
                        if (isset($edit_raw) && $edit_raw['material_id'] === $item['material_id']) continue;
                    ?>
                    <tr>
                        <td><?= $item['material_id'] ?></td>
                        <td><?= $item['material'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td class="actions">
                            <a href="?view=<?= $_GET['view'] ?>&edit_raw=<?= $item['material_id'] ?>"><button class="btn-edit">✏</button></a>
                            <a href="?view=<?= $_GET['view'] ?>&delete_raw=<?= $item['material_id'] ?>&category=<?= $_GET['view'] ?>" onclick="return confirm('Delete this material?')"><button class="btn-delete">🗑</button></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="../lib/js/main.js"></script>
</body>
</html>
