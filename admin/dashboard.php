<?php
require_once '../config/database.php';
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Mengambil statistik untuk dashboard
$totalAccounts = $database->accounts->countDocuments();
$totalUsers = $database->users->countDocuments(['role' => 'user']);
$totalTransactions = $database->transactions->countDocuments();
$recentTransactions = $database->transactions->aggregate([
    [
        '$lookup' => [
            'from' => 'users',
            'localField' => 'user_id',
            'foreignField' => '_id',
            'as' => 'user'
        ]
    ],
    [
        '$lookup' => [
            'from' => 'accounts',
            'localField' => 'account_id',
            'foreignField' => '_id',
            'as' => 'account'
        ]
    ],
    ['$sort' => ['transaction_date' => -1]],
    ['$limit' => 5]
])->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .dashboard-card {
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white border-r">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <img class="w-auto h-8" src="../assets/images/logo.png" alt="VPoint Logo">
                        <span class="ml-2 text-xl font-bold text-gray-800">VPoint</span>
                    </div>
                    <div class="mt-8">
                        <nav class="px-3">
                            <div class="space-y-1">
                                <a href="dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                                    <i class="fas fa-home w-5 h-5 mr-3"></i>
                                    Dashboard
                                </a>
                                <a href="accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-gamepad w-5 h-5 mr-3 text-gray-400"></i>
                                    Akun Game
                                </a>
                                <a href="transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
                                    Transaksi
                                </a>
                                <a href="users/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-users w-5 h-5 mr-3 text-gray-400"></i>
                                    Users
                                </a>
                            </div>
                            <div class="mt-auto pt-4 pb-3 border-t border-gray-200">
                                <a href="../logout.php" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                    Logout
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto focus:outline-none">
            <main class="flex-1 relative pb-8 z-0 overflow-y-auto">
                <!-- Page header -->
                <div class="bg-white shadow">
                    <div class="px-4 sm:px-6 lg:max-w-7xl lg:mx-auto lg:px-8">
                        <div class="py-6 md:flex md:items-center md:justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <div>
                                        <div class="flex items-center">
                                            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                                Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>
                                            </h1>
                                        </div>
                                        <dl class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap">
                                            <dt class="sr-only">Role</dt>
                                            <dd class="flex items-center text-sm text-gray-500 font-medium sm:mr-6">
                                                <i class="fas fa-shield-alt flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"></i>
                                                Administrator
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Total Accounts Card -->
                            <div class="dashboard-card bg-white overflow-hidden rounded-lg shadow">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-md p-3">
                                            <i class="fas fa-gamepad text-white text-xl"></i>
                                        </div>
                                        <div class="ml-5">
                                            <p class="text-sm font-medium text-gray-500 truncate">Total Akun</p>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($totalAccounts); ?></p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="accounts/" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            View all
                                            <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Users Card -->
                            <div class="dashboard-card bg-white overflow-hidden rounded-lg shadow">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-gradient-to-r from-green-500 to-green-600 rounded-md p-3">
                                            <i class="fas fa-users text-white text-xl"></i>
                                        </div>
                                        <div class="ml-5">
                                            <p class="text-sm font-medium text-gray-500 truncate">Total Users</p>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($totalUsers); ?></p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="users/" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-500">
                                            View all
                                            <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Transactions Card -->
                            <div class="dashboard-card bg-white overflow-hidden rounded-lg shadow">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-gradient-to-r from-purple-500 to-purple-600 rounded-md p-3">
                                            <i class="fas fa-exchange-alt text-white text-xl"></i>
                                        </div>
                                        <div class="ml-5">
                                            <p class="text-sm font-medium text-gray-500 truncate">Total Transaksi</p>
                                            <p class="mt-1 text-3xl font-semibold text-gray-900"><?php echo number_format($totalTransactions); ?></p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="transactions/" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-500">
                                            View all
                                            <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="mt-8">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-medium text-gray-900">Transaksi Terbaru</h2>
                                        <a href="transactions/" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                            View all
                                        </a>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php foreach ($recentTransactions as $transaction): ?>
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <a href="transactions/view.php?id=<?php echo $transaction->_id; ?>" class="text-indigo-600 hover:text-indigo-900">
                                                        <?php echo (string)$transaction->_id; ?>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($transaction->user[0]->username ?? 'N/A'); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($transaction->account[0]->account_name ?? 'N/A'); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo $transaction->transaction_date->toDateTime()->format('d/m/Y H:i'); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php
                                                    $statusClasses = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'processing' => 'bg-blue-100 text-blue-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $statusClass = $statusClasses[$transaction->status] ?? 'bg-gray-100 text-gray-800';
                                                    ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($transaction->status); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    Rp <?php echo number_format($transaction->payment_details->amount, 0, ',', '.'); ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile menu button -->
    <div class="fixed bottom-4 right-4 md:hidden">
        <button type="button" id="mobile-menu-button" class="bg-indigo-600 p-3 rounded-full text-white shadow-lg hover:bg-indigo-700 focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="fixed inset-0 z-40 hidden">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="mobile-menu-overlay"></div>
        <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-xl">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                    <span class="text-xl font-semibold text-gray-800">VPoint Admin</span>
                    <button type="button" id="mobile-menu-close" class="text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                    <a href="dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-gamepad w-5 h-5 mr-3 text-gray-400"></i>
                        Akun Game
                    </a>
                    <a href="transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
                        Transaksi
                    </a>
                    <a href="users/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-users w-5 h-5 mr-3 text-gray-400"></i>
                        Users
                    </a>
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <a href="../logout.php" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                            Logout
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        function toggleMobileMenu() {
            mobileMenu.classList.toggle('hidden');
        }

        mobileMenuButton?.addEventListener('click', toggleMobileMenu);
        mobileMenuClose?.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay?.addEventListener('click', toggleMobileMenu);
    </script>
</body>
</html>