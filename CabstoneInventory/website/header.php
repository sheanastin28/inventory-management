<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="bg-white shadow-sm py-4 px-6 md:px-12 lg:px-24 flex items-center justify-between rounded-b-lg">
  <div class="text-2xl font-bold text-gray-900">CABSTONE</div>

  <!-- Navigation Links -->
  <div class="hidden md:flex space-x-8">
    <a href="cabstone_site.php" class="text-gray-600 hover:text-gray-900 font-medium">HOME</a>
    <a href="cabstone_site.php#featured-products" class="text-gray-600 hover:text-gray-900 font-medium">SHOP</a>
    <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">ORDER</a>
    <a href="#" class="text-gray-600 hover:text-gray-900 font-medium">ABOUT US</a>
  </div>

  <!-- User Account Section -->
  <div class="relative">
    <?php if (isset($_SESSION['name'])): ?>
      <button onclick="toggleDropdown()" class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-md hover:bg-gray-200">
        <span class="text-gray-700 font-semibold"><?= htmlspecialchars($_SESSION['name']) ?></span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <!-- Dropdown Menu -->
      <div id="dropdownMenu" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-48 z-50">
        <a href="manage_account.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Manage Account</a>
        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
      </div>
    <?php else: ?>
      <a href="login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">Login</a>
    <?php endif; ?>
  </div>
</nav>

<script>
  function toggleDropdown() {
    const dropdown = document.getElementById('dropdownMenu');
    dropdown.classList.toggle('hidden');
  }

  // Close dropdown if clicked outside
  document.addEventListener('click', function(event) {
    const button = event.target.closest('button');
    const dropdown = document.getElementById('dropdownMenu');
    if (!button && !event.target.closest('#dropdownMenu')) {
      dropdown.classList.add('hidden');
    }
  });
</script>
