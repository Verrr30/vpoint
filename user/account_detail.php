<?php
session_start();
require_once '../config/database.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

// Cek ID akun
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data akun
$account = $database->accounts->findOne([
    '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
]);

// Jika akun tidak ditemukan
if (!$account) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data user
$user = $database->users->findOne([
    '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Akun - VPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1">
            <?php include 'includes/topbar.php'; ?>

            <div class="p-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Detail Akun</h2>
                        <a href="dashboard.php" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-200">
                                <?php if (isset($account->images->main_image)): ?>
                                    <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                         alt="<?php echo htmlspecialchars($account->account_name); ?>"
                                         id="mainImage"
                                         class="object-cover w-full h-full">
                                <?php else: ?>
                                    <img src="../assets/images/no-image.png" alt="No Image" id="mainImage" class="object-cover w-full h-full">
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($account->images->gallery) && !empty($account->images->gallery)): ?>
                            <div class="grid grid-cols-4 gap-2">
                                <?php foreach ($account->images->gallery as $image): ?>
                                    <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $image; ?>" 
                                         alt="Gallery Image"
                                         onclick="changeMainImage(this.src)"
                                         class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-6">
                            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($account->account_name); ?></h1>
                            
                            <div class="flex items-center space-x-4">
                                <span class="text-2xl font-bold text-blue-600">Rp <?php echo number_format($account->price, 0, ',', '.'); ?></span>
                                <?php if ($account->status === 'available'): ?>
                                    <span class="px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">Tersedia</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full">Terjual</span>
                                <?php endif; ?>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                                    <i class="fas fa-server text-gray-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Server ID</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($account->server_id); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                                    <i class="fas fa-star text-gray-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Level</p>
                                        <p class="font-medium"><?php echo $account->level; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                                    <i class="fas fa-trophy text-gray-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Rank</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($account->rank); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                                    <i class="fas fa-calendar text-gray-500"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                                        <p class="font-medium"><?php echo date('d M Y', $account->created_at->toDateTime()->getTimestamp()); ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($account->description)): ?>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Deskripsi</h3>
                                <p class="text-gray-600 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($account->description)); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="space-y-3">
                                <?php if ($account->status === 'available'): ?>
                                    <a href="transactions/checkout.php?id=<?php echo $account->_id; ?>" 
                                       class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fas fa-shopping-cart mr-2"></i> Beli Sekarang
                                    </a>
                                    <button class="w-full inline-flex justify-center items-center px-6 py-3 border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-lg transition-colors wishlist-btn"
                                            data-id="<?php echo $account->_id; ?>">
                                        <i class="far fa-heart mr-2"></i> Tambah ke Wishlist
                                    </button>
                                <?php else: ?>
                                    <button class="w-full px-6 py-3 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed" disabled>
                                        Akun Tidak Tersedia
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // Wishlist functionality
    document.querySelector('.wishlist-btn')?.addEventListener('click', function() {
        const accountId = this.dataset.id;
        // Add your wishlist logic here
        this.innerHTML = '<i class="fas fa-heart"></i> Ditambahkan ke Wishlist';
        this.classList.add('bg-gray-100');
    });
    </script>

    <script src="../assets/js/user.js"></script>
</body>
</html>