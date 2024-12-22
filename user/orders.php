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

// Ambil daftar transaksi user
$transactions = $database->transactions->aggregate([
    [
        '$match' => [
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
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
    [
        '$unwind' => '$account'
    ],
    [
        '$sort' => ['transaction_date' => -1]
    ]
])->toArray();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - VPoint</title>
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
                        <a href="dashboard.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="orders.php" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
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
                    <a href="dashboard.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Dashboard</a>
                    <a href="orders.php" class="bg-blue-50 border-blue-500 text-blue-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Pesanan Saya</a>
                    <a href="wishlist.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Wishlist</a>
                    <a href="profile.php" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Profil</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Pesanan</h2>
                <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 mb-4">Belum ada pesanan</p>
                    <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Mulai Berbelanja
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($transactions as $transaction): ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <!-- Order Header -->
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
                                            <?php echo date('d M Y H:i', $transaction->transaction_date->toDateTime()->getTimestamp()); ?>
                                        </p>
                                        <p class="text-sm font-medium text-gray-900">
                                            ID: <?php echo substr($transaction->_id, -8); ?>
                                        </p>
                                    </div>
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold <?php 
                                        echo $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                            ($transaction->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                            ($transaction->status === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')); 
                                    ?>">
                                        <?php 
                                        $statusLabels = [
                                            'pending' => 'Menunggu Pembayaran',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            'refunded' => 'Dikembalikan'
                                        ];
                                        echo $statusLabels[$transaction->status]; 
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Order Content -->
                            <div class="p-6">
                                <div class="flex items-center space-x-6">
                                    <!-- Account Image -->
                                    <div class="flex-shrink-0 w-24 h-24">
                                        <?php if (isset($transaction->account->images->main_image)): ?>
                                            <img class="w-full h-full object-cover rounded-lg" 
                                                 src="../uploads/accounts/<?php echo $transaction->account->_id; ?>/<?php echo $transaction->account->images->main_image; ?>" 
                                                 alt="<?php echo htmlspecialchars($transaction->account->account_name); ?>">
                                        <?php else: ?>
                                            <img class="w-full h-full object-cover rounded-lg" 
                                                 src="../assets/images/no-image.png" 
                                                 alt="No Image">
                                        <?php endif; ?>
                                    </div>

                                    <!-- Account Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <?php echo htmlspecialchars($transaction->account->account_name); ?>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Server ID: <?php echo htmlspecialchars($transaction->account->server_id); ?>
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Metode Pembayaran: <?php echo ucwords(str_replace('_', ' ', $transaction->payment_details->method)); ?>
                                        </p>
                                        <p class="mt-2 text-lg font-semibold text-gray-900">
                                            Rp <?php echo number_format($transaction->payment_details->amount, 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex-shrink-0 space-y-3">
                                        <a href="transactions/view.php?id=<?php echo $transaction->_id; ?>" 
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                                        </a>
                                        <?php if ($transaction->status === 'pending'): ?>
                                            <a href="transactions/upload_payment.php?id=<?php echo $transaction->_id; ?>" 
                                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <i class="fas fa-upload mr-2"></i> Upload Bukti
                                            </a>
                                            <a href="transactions/cancel.php?id=<?php echo $transaction->_id; ?>" 
                                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                               onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                <i class="fas fa-times mr-2"></i> Batalkan
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>