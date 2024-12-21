<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize default user data
$userData = [
    'username' => 'Guest',
    'avatar' => null
];

// Check if user is logged in and get user data
if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../config/database.php';
        
        $userDoc = $database->users->findOne([
            '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
        ]);
        
        if ($userDoc) {
            $userData = [
                'username' => $userDoc->username ?? 'Guest',
                'avatar' => $userDoc->avatar ?? null
            ];
        }
    } catch (Exception $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left side: Logo and Brand -->
            <div class="flex items-center">
                <a href="/vpoint/user/dashboard.php" class="flex items-center">
                    <img src="/vpoint/assets/images/logo.png" alt="VPoint Logo" class="h-8 w-auto">
                    <span class="ml-2 text-xl font-bold text-gray-900">VPoint</span>
                </a>
            </div>

            <!-- Center: Navigation Links -->
            <div class="hidden md:flex items-center space-x-4">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                $nav_items = [
                    'dashboard.php' => ['Dashboard', 'fa-home'],
                    'orders.php' => ['Pesanan Saya', 'fa-shopping-cart'],
                    'wishlist.php' => ['Wishlist', 'fa-heart'],
                    'profile.php' => ['Profil', 'fa-user']
                ];
                
                foreach ($nav_items as $page => $item) {
                    $active = $current_page === $page;
                    $classes = $active 
                        ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium text-blue-600 bg-blue-50' 
                        : 'flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 transition-colors';
                    ?>
                    <a href="/vpoint/user/<?php echo $page; ?>" class="<?php echo $classes; ?>">
                        <i class="fas <?php echo $item[1]; ?> mr-2"></i>
                        <?php echo $item[0]; ?>
                    </a>
                <?php } ?>
            </div>

            <!-- Right side: User Menu -->
            <div class="flex items-center space-x-4">
                <span class="hidden md:inline-block text-sm text-gray-700">
                    Welcome, <?php echo htmlspecialchars($userData['username']); ?>
                </span>
                
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button type="button" class="flex items-center space-x-2 focus:outline-none" id="user-menu-button">
                        <?php if ($userData['avatar']): ?>
                            <img src="/vpoint/uploads/avatars/<?php echo htmlspecialchars($userData['avatar']); ?>" 
                                 alt="User Avatar" 
                                 class="h-8 w-8 rounded-full object-cover border-2 border-gray-200">
                        <?php else: ?>
                            <img src="/vpoint/assets/images/default-avatar.png" 
                                 alt="Default Avatar" 
                                 class="h-8 w-8 rounded-full object-cover border-2 border-gray-200">
                        <?php endif; ?>
                        <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50" 
                         id="user-menu-dropdown">
                        <a href="/vpoint/user/profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                            Profile
                        </a>
                        <a href="/vpoint/logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2 text-red-500"></i>
                            Logout
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none" 
                        id="mobile-menu-button">
                    <i class="fas fa-bars w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <?php foreach ($nav_items as $page => $item) { 
                    $active = $current_page === $page;
                    $classes = $active 
                        ? 'block px-3 py-2 rounded-md text-base font-medium text-blue-600 bg-blue-50' 
                        : 'block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50';
                    ?>
                    <a href="/vpoint/user/<?php echo $page; ?>" class="<?php echo $classes; ?>">
                        <i class="fas <?php echo $item[1]; ?> mr-2"></i>
                        <?php echo $item[0]; ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile Dropdown Toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    if (userMenuButton && userMenuDropdown) {
        userMenuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });
    }

    // Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
        if (userMenuDropdown && !userMenuButton?.contains(event.target) && !userMenuDropdown?.contains(event.target)) {
            userMenuDropdown.classList.add('hidden');
        }
        
        if (mobileMenu && !mobileMenuButton?.contains(event.target) && !mobileMenu?.contains(event.target)) {
            mobileMenu.classList.add('hidden');
        }
    });
});
</script>
</body>
</html>