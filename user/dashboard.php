<?php
session_start();
require_once '../config/database.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil data user
$user = $database->users->findOne([
    '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
]);

// Ambil daftar akun game yang tersedia
$accounts = $database->accounts->find([
    'status' => 'available'
], [
    'sort' => ['created_at' => -1]
]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VPoint User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="font-inter bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="dashboard.php" class="text-xl font-bold text-blue-600">VPoint</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="dashboard.php" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="orders.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Pesanan Saya
                        </a>
                        <a href="wishlist.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Wishlist
                        </a>
                        <a href="profile.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Profil
                        </a>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-sm text-gray-700 mr-4">Welcome, <?php echo htmlspecialchars($user->username); ?></span>
                    </div>
                    <div class="ml-3 relative">
                        <div class="flex items-center">
                            <img src="../assets/images/default-avatar.png" alt="User Avatar" class="h-8 w-8 rounded-full">
                            <a href="../logout.php" class="ml-4 text-sm text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="sm:hidden hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="dashboard.php" class="bg-blue-50 border-blue-500 text-blue-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Dashboard</a>
                    <a href="orders.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Pesanan Saya</a>
                    <a href="wishlist.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Wishlist</a>
                    <a href="profile.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Profil</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                            <i class="fas fa-shopping-cart text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
                            <p class="text-2xl font-semibold text-gray-900" id="total-orders">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                            <i class="fas fa-wallet text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Saldo</p>
                            <p class="text-2xl font-semibold text-gray-900" id="balance">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>
<!-- Filter dan Pencarian -->
<div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Filter Pencarian</h3>
                    </div>
                    <form id="filterForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Filter Harga -->
                            <div>
                                <label for="hargaMinimal" class="block text-sm font-medium text-gray-700 mb-1">Harga Minimal</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="hargaMinimal" id="hargaMinimal" 
                                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-4 sm:text-sm border-gray-300 rounded-md" 
                                           placeholder="Minimal">
                                </div>
                            </div>
                            <div>
                                <label for="hargaMaksimal" class="block text-sm font-medium text-gray-700 mb-1">Harga Maksimal</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="hargaMaksimal" id="hargaMaksimal" 
                                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-4 sm:text-sm border-gray-300 rounded-md" 
                                           placeholder="Maksimal">
                                </div>
                            </div>
                            <!-- Tombol Aksi -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-search mr-2"></i> Cari
                                </button>
                                <button type="button" id="resetFilter" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-undo mr-2"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Available Accounts Section -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Akun Game Tersedia</h2>
                </div>
                <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="accountsContainer">
    <?php foreach ($accounts as $account): ?>
    <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-300 account-card" 
         data-harga="<?php echo $account->price; ?>">
                            <div class="aspect-w-16 aspect-h-9">
                                <?php if (isset($account->images->main_image) && $account->images->main_image): ?>
                                    <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                         alt="<?php echo htmlspecialchars($account->account_name); ?>"
                                         class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <img src="../assets/images/no-image.png" 
                                         alt="No Image"
                                         class="w-full h-48 object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($account->account_name); ?>
                                </h3>
                                <div class="space-y-2 mb-4">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Server ID:</span> 
                                        <?php echo htmlspecialchars($account->server_id); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Level:</span> 
                                        <?php echo $account->level; ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Rank:</span> 
                                        <?php echo htmlspecialchars($account->rank); ?>
                                    </p>
                                    <p class="text-lg font-semibold text-blue-600">
                                        Rp <?php echo number_format($account->price, 0, ',', '.'); ?>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="account_detail.php?id=<?php echo $account->_id; ?>" 
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-eye mr-2"></i> Detail
                                    </a>
                                    <a href="transactions/checkout.php?id=<?php echo $account->_id; ?>" 
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-shopping-cart mr-2"></i> Beli
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Fungsi untuk memformat angka ke format rupiah
        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Fungsi untuk mengupdate statistik
        function updateUserStats() {
            fetch('api/get_user_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    document.getElementById('total-orders').textContent = data.total_orders;
                    document.getElementById('balance').textContent = formatRupiah(data.balance);
                })
                .catch(error => console.error('Error:', error));
        }

        // Update statistik setiap 5 detik
        updateUserStats(); // Update pertama kali
        setInterval(updateUserStats, 5000);

        // Filter Functionality
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hargaMinimal = parseInt(document.getElementById('hargaMinimal').value) || 0;
            const hargaMaksimal = parseInt(document.getElementById('hargaMaksimal').value) || Infinity;
            
            const cards = document.querySelectorAll('.account-card');
            let hasResults = false;

            cards.forEach(card => {
                const hargaText = card.querySelector('.text-blue-600').textContent;
                const harga = parseInt(hargaText.replace(/[^0-9]/g, ''));
                
                if (harga >= hargaMinimal && (hargaMaksimal === Infinity || harga <= hargaMaksimal)) {
                    card.style.display = 'block';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle pesan tidak ada hasil
            const noResults = document.getElementById('noResults');
            if (!hasResults) {
                if (!noResults) {
                    const noResultsDiv = document.createElement('div');
                    noResultsDiv.id = 'noResults';
                    noResultsDiv.className = 'text-center py-8';
                    noResultsDiv.innerHTML = `
                        <i class="fas fa-search text-gray-400 text-4xl mb-3 block"></i>
                        <p class="text-gray-500">Tidak ada akun yang sesuai dengan filter yang dipilih</p>
                        <p class="text-gray-400 text-sm mt-2">Rentang harga: ${formatRupiah(hargaMinimal)} - ${hargaMaksimal === Infinity ? 'Tidak terbatas' : formatRupiah(hargaMaksimal)}</p>
                    `;
                    document.getElementById('accountsContainer').insertAdjacentElement('afterend', noResultsDiv);
                }
            } else if (noResults) {
                noResults.remove();
            }
        });

        // Reset Filter
        document.getElementById('resetFilter').addEventListener('click', function() {
            document.getElementById('hargaMinimal').value = '';
            document.getElementById('hargaMaksimal').value = '';
            
            // Tampilkan semua kartu
            document.querySelectorAll('.account-card').forEach(card => {
                card.style.display = 'block';
            });
            
            // Hapus pesan tidak ada hasil jika ada
            const noResults = document.getElementById('noResults');
            if (noResults) {
                noResults.remove();
            }
        });
    </script>
</body>
</html>