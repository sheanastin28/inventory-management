<?php
session_start();
include '../lib/db/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CABSTONE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #fdfaf7;
    }
  </style>
</head>
<body class="text-gray-800">

  <?php include 'header.php'; ?>

  <header class="relative flex items-center justify-center min-h-[600px] py-16 px-6 md:px-12 lg:px-24 hero-background">
    <div class="absolute inset-0 flex flex-col md:flex-row items-center justify-start max-w-7xl mx-auto">
      <div class="w-full md:w-1/2 text-left p-4 md:p-8">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
          Transform Your Space with Modular Cabinets
        </h1>
        <p class="text-lg md:text-xl text-gray-700 mb-8 max-w-md">
          Dreaming of a space that perfectly blends style and functionality? At <b>CABSTONE</b>, we specialize in modular cabinets that transform any room from concept to delivery.
        </p>
        <a href="#featured-products" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 ease-in-out">
          Explore Products
        </a>
      </div>
      <div class="hidden md:block md:w-1/2 p-4">
        <img src="../.assets/kitchen.jpg" alt="Modular Cabinets" class="rounded-lg shadow-xl max-w-full h-auto">
      </div>
    </div>
  </header>

  <section id="featured-products" class="container mx-auto py-16 px-6 md:px-12 lg:px-24">
    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-12">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <?php

      $finfo = extension_loaded('fileinfo') ? new finfo(FILEINFO_MIME_TYPE) : null;

      $result = $conn->query("SELECT product_id, product, image, sold FROM product");

      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $imageMime = 'image/png';
              $base64 = '';
              if ($row['image']) {
                  if ($finfo) {
                      $mime = $finfo->buffer($row['image']);
                      if (str_starts_with($mime, 'image/')) {
                          $imageMime = $mime;
                      }
                  }
                  $base64 = base64_encode($row['image']);
              }

              echo '<a href="product_detail.php?id=' . htmlspecialchars($row['product_id']) . '" class="block bg-white rounded-lg shadow-md overflow-hidden text-center transform hover:scale-105 transition duration-300 ease-in-out">';
              echo '  <img src="data:' . $imageMime . ';base64,' . $base64 . '" class="w-full h-48 object-contain">';
              echo '  <div class="p-4">';
              echo '    <h3 class="font-semibold text-lg text-gray-900 mb-2">' . htmlspecialchars($row['product']) . '</h3>';
              echo '    <p class="text-gray-600 text-sm mb-4">Sold: ' . htmlspecialchars($row['sold']) . '</p>';
              echo '  </div>';
              echo '</a>';
          }
      } else {
          echo '<p class="col-span-full text-center text-gray-500">No products found.</p>';
      }

      $conn->close();
      ?>
    </div>
  </section>

</body>
</html>
