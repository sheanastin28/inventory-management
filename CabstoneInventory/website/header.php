<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="bg-white shadow-sm py-4 px-6 md:px-12 lg:px-24 flex items-center justify-between rounded-b-lg">
  <div class="text-2xl font-bold text-gray-900">CABSTONE</div>

  <div class="hidden md:flex space-x-8">
    <a href="cabstone_site.php" class="text-gray-600 hover:text-gray-900 font-medium">HOME</a>
    <a href="cabstone_site.php#featured-products" class="text-gray-600 hover:text-gray-900 font-medium">SHOP</a>
    <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">ORDER</a>
    <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">ABOUT US</a>
  </div>

  <div class="flex items-center gap-4">
    <?php if (isset($_SESSION['name'])): ?>
      <span class="text-gray-700">Hello, <?= htmlspecialchars($_SESSION['name']) ?></span>
      <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Logout</a>
    <?php else: ?>
      <a href="login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">Login</a>
    <?php endif; ?>
  </div>
</nav>
