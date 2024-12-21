<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <button id="sidebar-toggle" class="p-2 rounded-md lg:hidden hover:bg-gray-100">
                <i class="fas fa-bars text-gray-600"></i>
            </button>
            <div class="flex items-center space-x-4">
                <?php if (isset($user)): ?>
                    <span class="text-sm text-gray-700">Welcome, <?php echo htmlspecialchars($user->username); ?></span>
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <?php if (isset($user->avatar) && $user->avatar): ?>
                                <img src="../uploads/avatars/<?php echo $user->avatar; ?>" alt="User Avatar" class="h-8 w-8 rounded-full">
                            <?php else: ?>
                                <img src="../assets/images/default-avatar.png" alt="Default Avatar" class="h-8 w-8 rounded-full">
                            <?php endif; ?>
                            <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle dropdown menu
    const dropdownButton = document.querySelector('button.flex.items-center');
    const dropdownMenu = document.querySelector('.absolute.right-0');
    
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });
</script>