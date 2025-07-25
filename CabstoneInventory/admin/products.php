<?php
session_start();
include '../lib/db/db.php';

// --- Pagination Setup ---
$limit = 3;
$page_product = isset($_GET['page_product']) ? (int)$_GET['page_product'] : 1;
$page_image = isset($_GET['page_image']) ? (int)$_GET['page_image'] : 1;

$offset_product = ($page_product - 1) * $limit;
$offset_image = ($page_image - 1) * $limit;

// --- Add Product ---
if (isset($_POST['add_product'])) {
    $product = $_POST['product'];
    $description = $_POST['description'];
    $imageData = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }
    $stmt = $conn->prepare("INSERT INTO product (product, description, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $product, $description, $imageData);
    $stmt->execute();
}

// --- Edit Product ---
if (isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $product = $_POST['product'];
    $description = $_POST['description'];

    if (!empty($_FILES['edit_image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['edit_image']['tmp_name']);
        $stmt = $conn->prepare("UPDATE product SET product = ?, description = ?, image = ? WHERE product_id = ?");
        $stmt->bind_param("ssss", $product, $description, $imageData, $id);
    } else {
        $stmt = $conn->prepare("UPDATE product SET product = ?, description = ? WHERE product_id = ?");
        $stmt->bind_param("sss", $product, $description, $id);
    }
    $stmt->execute();
}

// --- Delete Product ---
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $conn->query("DELETE FROM product WHERE product_id = '$id'");
}

// --- Add Image ---
if (isset($_POST['add_image'])) {
    $product_id = $_POST['product_id'];
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $stmt = $conn->prepare("INSERT INTO product_image (image, product_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $imageData, $product_id);
    $stmt->execute();
}

// --- Edit Image ---
if (isset($_POST['edit_image'])) {
    $img_id = $_POST['img_id'];
    $product_id = $_POST['product_id'];

    if (!empty($_FILES['new_image']['tmp_name'])) {
        $newImageData = file_get_contents($_FILES['new_image']['tmp_name']);
        $stmt = $conn->prepare("UPDATE product_image SET image = ?, product_id = ? WHERE img_id = ?");
        $stmt->bind_param("sss", $newImageData, $product_id, $img_id);
    } else {
        $stmt = $conn->prepare("UPDATE product_image SET product_id = ? WHERE img_id = ?");
        $stmt->bind_param("ss", $product_id, $img_id);
    }
    $stmt->execute();
}

// --- Delete Image ---
if (isset($_GET['delete_image'])) {
    $id = $_GET['delete_image'];
    $conn->query("DELETE FROM product_image WHERE img_id = '$id'");
}

// --- Fetch Data ---
$products = $conn->query("SELECT * FROM product LIMIT $limit OFFSET $offset_product");
$images = $conn->query("SELECT product_image.*, product.product, product.product_id FROM product_image JOIN product ON product_image.product_id = product.product_id LIMIT $limit OFFSET $offset_image");

// --- Total Pages ---
$total_products = $conn->query("SELECT COUNT(*) AS total FROM product")->fetch_assoc()['total'];
$total_images = $conn->query("SELECT COUNT(*) AS total FROM product_image")->fetch_assoc()['total'];

$total_product_pages = ceil($total_products / $limit);
$total_image_pages = ceil($total_images / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link rel="stylesheet" href="../lib/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .img-preview {
            width: 80px;
            margin-top: 10px;
            display: block;
        }
        table td, table th {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
<?php include 'header.php'; ?>

<div class="container-fluid mt-4">
    <h2 class="text-left mb-4 fw-bold">Product Management</h2>
    <div class="row row-cols-1 row-cols-md-2 g-4">


    <!-- Products Box -->
    <div class="col-md-6" >
        <div class="card p-3" style="box-shadow: 0 5px 12px rgba(0, 0, 0, 1);">
            <div class="d-flex justify-content-between align-items-center mb-3" >
                <h5>All Products</h5>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>
            </div>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr><th style="width:50px;">ID</th><th style="width:120px;">Product</th><th style="width:80px;">Image</th><th>Description</th><th>Action</th></tr>
                </thead>
                <tbody>
                  <?php while($row = $products->fetch_assoc()): ?>
                    <tr>
                      <td><?= $row['product_id'] ?></td>
                      <td><?= htmlspecialchars($row['product']) ?></td>
                      <td>
                        <?php if (!empty($row['image'])): ?>
                          <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" style="width: 60px;">
                        <?php else: ?>
                          <span>No Image</span>
                        <?php endif; ?>
                      </td>
                      <!-- Scrollable Description -->
                      <td style="max-width: 200px;">
                        <div style="max-height: 80px; overflow-y: auto; white-space: normal; word-wrap: break-word; border: 1px solid #ddd; padding: 4px;">
                          <?= htmlspecialchars($row['description']) ?>
                        </div>
                      </td>
                      <td>
                        <button class="btn btn-link p-0"
                          onclick="openEditModal('<?= $row['product_id'] ?>', `<?= addslashes($row['product']) ?>`, `<?= addslashes($row['description']) ?>`, `<?= !empty($row['image']) ? 'data:image/jpeg;base64,' . base64_encode($row['image']) : '' ?>`)">
                          Edit
                        </button>
                        |
                        <a class="text-danger"
                          href="?delete_product=<?= $row['product_id'] ?>&page_product=<?= $page_product ?>&page_image=<?= $page_image ?>"
                          onclick="return confirm('Delete?')">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
            </table>
            <nav>
              <ul class="pagination pagination-sm justify-content-center">
                <?php for ($i = 1; $i <= $total_product_pages; $i++): ?>
                  <li class="page-item <?= $i == $page_product ? 'active' : '' ?>">
                    <a class="page-link" href="?page_product=<?= $i ?>&page_image=<?= $page_image ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
        </div>
    </div>

    <!-- Product Images Box -->
    <div class="col-md-6">
        <div class="card p-3" style="box-shadow: 0 5px 12px rgba(0, 0, 0, 1);">
            <div class="d-flex justify-content-between align-items-center">
                <h5>All Product Images</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addImageModal"  box-shadow: 0 5px 12px rgba(0, 0, 0, 1);>+ Add Image</button>
            </div>
            <table class="table table-bordered table-sm mt-2">
                <thead>
                    <tr><th>ID</th><th>Image</th><th>Product</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php while($img = $images->fetch_assoc()): ?>
                    <tr>
                        <td><?= $img['img_id'] ?></td>
                        <td><img src="view_image.php?id=<?= $img['img_id'] ?>" class="img-thumbnail" style="width:60px;"></td>
                        <td><?= htmlspecialchars($img['product']) ?></td>
                        <td>
                            <button class="btn btn-link p-0"
                              onclick="openEditImageModal('<?= $img['img_id'] ?>','<?= $img['product_id'] ?>','view_image.php?id=<?= $img['img_id'] ?>')">Edit</button> |
                            <a class="text-danger" href="?delete_image=<?= $img['img_id'] ?>&page_product=<?= $page_product ?>&page_image=<?= $page_image ?>" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <nav>
              <ul class="pagination pagination-sm justify-content-center">
                <?php for ($i = 1; $i <= $total_image_pages; $i++): ?>
                  <li class="page-item <?= $i == $page_image ? 'active' : '' ?>">
                    <a class="page-link" href="?page_product=<?= $page_product ?>&page_image=<?= $i ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
        </div>
    </div>

</div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" name="product" placeholder="Product Name" required>
        <textarea class="form-control mb-2" name="description" placeholder="Description" required></textarea>
        <input type="file" class="form-control mb-2" name="image" accept="image/*" onchange="previewNewImage(event, 'add_product_preview')">
        <img id="add_product_preview" class="img-preview" style="display:none;">
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Image Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Product Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <select name="product_id" class="form-control mb-2" required>
          <option value="">Select Product</option>
          <?php
          $allProducts = $conn->query("SELECT * FROM product");
          while ($p = $allProducts->fetch_assoc()):
          ?>
            <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['product']) ?></option>
          <?php endwhile; ?>
        </select>
        <input type="file" class="form-control mb-2" name="image" accept="image/*" onchange="previewNewImage(event, 'add_image_preview')">
        <img id="add_image_preview" class="img-preview" style="display:none;">
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_image" class="btn btn-primary">Add Image</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="product_id" id="edit_product_id">
        <input type="text" class="form-control mb-2" name="product" id="edit_product" required>
        <textarea class="form-control mb-2" name="description" id="edit_description" required></textarea>
        <img id="edit_product_preview" class="img-preview" alt="Current Image" style="display:none;">
        <input type="file" class="form-control mb-2" name="edit_image" accept="image/*" onchange="previewNewImage(event, 'edit_product_preview')">
      </div>
      <div class="modal-footer">
        <button type="submit" name="edit_product" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="img_id" id="edit_img_id">
        <select class="form-control mb-2" name="product_id" id="edit_image_product" required>
          <option value="">Select Product</option>
          <?php
          $allProducts2 = $conn->query("SELECT * FROM product");
          while($p2 = $allProducts2->fetch_assoc()):
          ?>
            <option value="<?= $p2['product_id'] ?>"><?= htmlspecialchars($p2['product']) ?></option>
          <?php endwhile; ?>
        </select>
        <img id="edit_image_preview" class="img-preview" alt="Current Image" style="display:none;">
        <input type="file" class="form-control mb-2" name="new_image" accept="image/*" onchange="previewNewImage(event, 'edit_image_preview')">
      </div>
      <div class="modal-footer">
        <button type="submit" name="edit_image" class="btn btn-primary">Update Image</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id, name, desc, img) {
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product').value = name;
    document.getElementById('edit_description').value = desc;
    const imgPreview = document.getElementById('edit_product_preview');
    if (img) {
        imgPreview.src = img;
        imgPreview.style.display = 'block';
    } else {
        imgPreview.style.display = 'none';
    }
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

function openEditImageModal(imgId, productId, imgSrc) {
    document.getElementById('edit_img_id').value = imgId;
    document.getElementById('edit_image_product').value = productId;
    const imgPreview = document.getElementById('edit_image_preview');
    imgPreview.src = imgSrc;
    imgPreview.style.display = 'block';
    new bootstrap.Modal(document.getElementById('editImageModal')).show();
}

function previewNewImage(event, previewId) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const imgPreview = document.getElementById(previewId);
        imgPreview.src = e.target.result;
        imgPreview.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
