<?php
session_start();

include '../lib/db/db.php';

// Fetch product
$product_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
if (empty($product_id)) {
    header("Location: error_page.php?type=invalid_product");
    exit();
}

$product = null;
$stmt = $conn->prepare("SELECT product, description, sold FROM product WHERE product_id = ?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $product = $res->fetch_assoc();
} else {
    header("Location: error_page.php?type=invalid_product");
    exit();
}
$stmt->close();

// Fetch product images
$images = [];
$stmt2 = $conn->prepare("SELECT image FROM product_image WHERE product_id = ? ORDER BY img_id ASC");
$stmt2->bind_param("s", $product_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($img = $res2->fetch_assoc()) {
    $images[] = $img['image'];
}
$stmt2->close();

// Handle inquiry form submission
$successMsg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inquire_submit"])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $cont_no = $_POST['cont_no'];
    $method = $_POST['com_method'];
    $length = $_POST['length'];
    $width = $_POST['width'];
    $height = $_POST['height'];
    $budget = $_POST['budget'];
    $query = $_POST['query'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id && !empty($product_id)) {
        $stmt = $conn->prepare("INSERT INTO inquire (email, cont_no, name, address, com_method, length, width, height, budget, query, user_id, product_id)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssssssssssss", $email, $cont_no, $name, $address, $method, $length, $width, $height, $budget, $query, $user_id, $product_id);

                
                if ($stmt->execute()) {
                    $successMsg = "Inquiry submitted successfully!";
                } else {
                    $successMsg = "Failed to submit inquiry.";
                }
                $stmt->close();
            } else {
                $successMsg = "You must be logged in to send an inquiry.";
            }
        }

$conn->close();

$finfo = extension_loaded('fileinfo') ? new finfo(FILEINFO_MIME_TYPE) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['product']) ?> - Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #fdfaf7; }
    .scroll-button {
      position: absolute; top: 50%; transform: translateY(-50%);
      background: rgba(0,0,0,0.5); color: white; padding: 10px;
      border-radius: 50%; cursor: pointer; z-index: 10;
    }
    .scroll-button.left { left: 10px; }
    .scroll-button.right { right: 10px; }
    .image-gallery {
      display: flex; overflow-x: scroll; scroll-snap-type: x mandatory;
    }
    .image-gallery img {
      scroll-snap-align: start; min-width: 100%;
      height: auto; object-fit: contain;
    }
    .modal-bg {
      background-color: rgba(0,0,0,0.5);
    }
  </style>
</head>
<body class="text-gray-800">
  <?php include 'header.php'; ?>

  <main class="container mx-auto py-12 px-6 md:px-12 lg:px-24">
    <?php if ($successMsg): ?>
      <div class="mb-6 text-green-600 font-semibold"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row gap-8">
      <div class="md:w-1/2 bg-white shadow-lg rounded-lg p-4 relative">
        <div class="image-gallery" id="gallery">
          <?php foreach ($images as $img):
              $mime = 'image/png';
              if (!empty($img)) {
                  if ($finfo) {
                      $detected = $finfo->buffer($img);
                      if (str_starts_with($detected, 'image/')) $mime = $detected;
                  }
                  $encoded = base64_encode($img);
              } else {
                  $encoded = '';
              }
          ?>
          <img src="data:<?= $mime ?>;base64,<?= $encoded ?>" class="rounded-lg">
          <?php endforeach; ?>
        </div>
        <?php if (count($images) > 1): ?>
          <div class="scroll-button left" onclick="scrollGallery(-1)">&#10094;</div>
          <div class="scroll-button right" onclick="scrollGallery(1)">&#10095;</div>
        <?php endif; ?>
      </div>

      <div class="md:w-1/2 bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-4xl font-bold mb-4"><?= htmlspecialchars($product['product']) ?></h1>
        <p class="text-lg text-gray-700 mb-6"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <div class="flex justify-between items-center">
          <span class="text-xl font-semibold"><?= htmlspecialchars($product['sold']) ?> sold</span>
          <button onclick="document.getElementById('modal').classList.remove('hidden')" class="bg-indigo-700 text-white px-6 py-3 rounded-full hover:bg-indigo-800">Inquire</button>
        </div>
      </div>
    </div>
  </main>

  <!-- MODAL -->
  <div id="modal" class="hidden fixed inset-0 flex justify-center items-center modal-bg z-50">
    <div class="bg-blue-200 p-2 rounded-2xl w-[90%] max-w-[700px]">
      <h2 class="text-2xl font-bold mb-4 text-center p-3"><?= htmlspecialchars($product['product']) ?> Form</h2>

      <form method="POST">
        <div class="space-y-3">
          <input type="email" name="email" placeholder="Email" class="w-full px-2 py-1 rounded" required>
          <input type="text" name="name" placeholder="Name" class="w-full px-2 py-1 rounded" required>
          <input type="text" name="address" placeholder="Address" class="w-full px-2 py-1 rounded" required>
          <input type="text" name="cont_no" placeholder="Contact Number" class="w-full px-2 py-1 rounded" required>
          
          <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <label>Preferred method of contact:</label>
            <select name="com_method" class="flex-1 px-4 py-1 rounded" required>
              <option value="Messenger">Messenger</option>
              <option value="Viber">Viber</option>
            </select>
          </div>

          <div class="font-semibold">Estimated Space Dimensions</div>
          <div class="grid grid-cols-3 gap-2">
            <input type="text" name="length" placeholder="Length" class="px-4 py-1 rounded" required>
            <input type="text" name="width" placeholder="Width" class="px-4 py-1 rounded" required>
            <input type="text" name="height" placeholder="Ceiling Height" class="px-4 py-1 rounded" required>
          </div>

          <select name="budget" class="w-full px-4 py-1 rounded" required>
            <option value="15000">₱15,000</option>
            <option value="30000">₱30,000</option>
            <option value="50000">₱50,000+</option>
          </select>

          <div>
            <label class="block mb-1 font-semibold">Additional Queries & Specifications:</label>
            <textarea name="query" rows="4" class="w-full p-3 rounded resize-none" required></textarea>
          </div>
        </div>

        <div class="mt-4 flex justify-center space-x-4">
          <button type="submit" name="inquire_submit" class="bg-gray-800 text-white px-6 py-2 rounded-full hover:bg-gray-900">Inquire</button>
          <button type="button" onclick="document.getElementById('modal').classList.add('hidden')" class="bg-red-500 text-white px-6 py-2 rounded-full hover:bg-red-600">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const gallery = document.getElementById('gallery');
    let index = 0;
    function scrollGallery(dir) {
      const imgWidth = gallery.children[0].offsetWidth;
      index = (index + dir + gallery.children.length) % gallery.children.length;
      gallery.scrollTo({ left: index * imgWidth, behavior: 'smooth' });
    }
  </script>
</body>
</html>
