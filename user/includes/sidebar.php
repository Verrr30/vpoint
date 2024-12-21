<div class="w-64 bg-gray-900 min-h-screen flex flex-col">
    <!-- Logo -->
    <div class="flex items-center px-6 py-4 bg-gray-800">
        <img src="../assets/images/logo.png" alt="VPoint Logo" class="h-8 w-auto">
        <span class="ml-3 text-xl font-semibold text-white">VPoint</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4">
        <a href="../user/dashboard.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-gray-800 text-white' : ''; ?>">
            <i class="fas fa-home w-5 h-5"></i>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="../user/orders.php" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'bg-gray-800 text-white' : ''; ?>">
            <i class="fas fa-shopping-cart w-5 h-5"></i>
            <span class="ml-3">Pesanan Saya</span>
        </a>

        <a href="../user/wishlist.php" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) === 'wishlist.php' ? 'bg-gray-800 text-white' : ''; ?>">
            <i class="fas fa-heart w-5 h-5"></i>
            <span class="ml-3">Wishlist</span>
        </a>

        <a href="../user/profile.php" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'bg-gray-800 text-white' : ''; ?>">
            <i class="fas fa-user w-5 h-5"></i>
            <span class="ml-3">Profil</span>
        </a>

        <div class="mt-auto">
            <a href="../logout.php" class="flex items-center px-4 py-3 mt-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-sign-out-alt w-5 h-5"></i>
                <span class="ml-3">Keluar</span>
            </a>
        </div>
    </nav>
</div>