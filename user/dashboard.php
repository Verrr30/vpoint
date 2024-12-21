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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <button id="sidebar-toggle" class="p-2 rounded-md lg:hidden hover:bg-gray-100">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">Welcome, <?php echo htmlspecialchars($user->username); ?></span>
                        <img src="../assets/images/default-avatar.png" alt="User Avatar" class="h-8 w-8 rounded-full">
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-6 py-8">
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

                    <!-- Available Accounts Section -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Akun Game Tersedia</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($accounts as $account): ?>
                                <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-300">
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
        </div>
    </div>

    <script>
        // Sidebar Toggle for Mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
    <script>
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
    setInterval(updateUserStats, 5000); // Update setiap 5 detik

    // Tambahkan ke script yang sudah ada
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle for Mobile (kode yang sudah ada)
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });

        // Update stats pertama kali
        updateUserStats();
    });
</script>
</body>
</html>
