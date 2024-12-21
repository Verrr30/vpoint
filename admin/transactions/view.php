<?php
require_once '../../config/database.php';
session_start();

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

// Ambil detail transaksi
$transaction = $database->transactions->aggregate([
    [
        '$match' => [
            '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
        ]
    ],
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
    ]
])->toArray();

if (empty($transaction)) {
    header('Location: index.php');
    exit();
}

$transaction = $transaction[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - VPoint Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .detail-card {
            transition: all 0.3s ease;
        }
        .detail-card:hover {
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
                        <img class="w-auto h-8" src="../../assets/images/logo.png" alt="VPoint Logo">
                        <span class="ml-2 text-xl font-bold text-gray-800">VPoint</span>
                    </div>
                    <div class="mt-8">
                        <nav class="px-3">
                            <div class="space-y-1">
                                <a href="../dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-home w-5 h-5 mr-3 text-gray-400"></i>
                                    Dashboard
                                </a>
                                <a href="../accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-gamepad w-5 h-5 mr-3 text-gray-400"></i>
                                    Akun Game
                                </a>
                                <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                                    <i class="fas fa-exchange-alt w-5 h-5 mr-3"></i>
                                    Transaksi
                                </a>
                                <a href="../users/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-users w-5 h-5 mr-3 text-gray-400"></i>
                                    Users
                                </a>
                            </div>
                            <div class="mt-auto pt-4 pb-3 border-t border-gray-200">
                                <a href="../../logout.php" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
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
                                <div class="flex items-center justify-between">
                                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                        Detail Transaksi
                                    </h1>
                                    <a href="index.php" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Transaction Information -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Transaksi</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">ID Transaksi</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo (string)$transaction->_id; ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo $transaction->transaction_date->toDateTime()->format('d/m/Y H:i'); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <?php
                                            $statusColors = [
                                                'completed' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusColor = $statusColors[strtolower($transaction->status)] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 <?php echo $statusColor; ?>">
                                                <?php echo $transaction->status; ?>
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Total</dt>
                                        <dd class="mt-1 text-sm text-gray-900">Rp <?php echo number_format($transaction->payment_details->amount, 0, ',', '.'); ?></dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Buyer Information -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembeli</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->user[0]->username); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->user[0]->email); ?></dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Account Information -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Akun</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nama Akun</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->account[0]->account_name); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Server ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->account[0]->server_id); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Level</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo $transaction->account[0]->level; ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rank</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->account[0]->rank); ?></dd>
                                    </div>
                                </dl>
                            </div>

                            <?php if (isset($transaction->payment_details)): ?>
                            <!-- Payment Details -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Detail Pembayaran</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->payment_details->method); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status Pembayaran</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($transaction->payment_details->status); ?></dd>
                                    </div>
                                    <?php if (isset($transaction->payment_details->proof_image)): ?>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Bukti Pembayaran</dt>
                                        <dd class="mt-1 text-sm text-indigo-600 hover:text-indigo-500">
                                            <a href="../../assets/images/payments/<?php echo $transaction->payment_details->proof_image; ?>" 
                                               target="_blank"
                                               class="inline-flex items-center">
                                                <i class="fas fa-image mr-2"></i>
                                                Lihat Bukti
                                            </a>
                                        </dd>
                                    </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                            <?php endif; ?>
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
                    <a href="../dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-home w-5 h-5 mr-3 text-gray-400"></i>
                        Dashboard
                    </a>
                    <a href="../accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-gamepad w-5 h-5 mr-3 text-gray-400"></i>
                        Akun Game
                    </a>
                    <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <i class="fas fa-exchange-alt w-5 h-5 mr-3"></i>
                        Transaksi
                    </a>
                    <a href="../users/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-users w-5 h-5 mr-3 text-gray-400"></i>
                        Users
                    </a>
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <a href="../../logout.php" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
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