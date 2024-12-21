<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /vpoint/login.php');
    exit();
}

try {
    // Get user's wishlist items
    $wishlist = $database->wishlist->find([
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
    ]);

    // Get account details for wishlist items
    $wishlistItems = [];
    foreach ($wishlist as $item) {
        $account = $database->accounts->findOne([
            '_id' => $item->account_id
        ]);
        if ($account) {
            $wishlistItems[] = $account;
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching wishlist items.";
    $wishlistItems = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - VPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="min-h-screen py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Wishlist Saya</h1>
            </div>

            <?php if (empty($wishlistItems)): ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-heart text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Wishlist Anda Kosong</h3>
                <p class="text-gray-500 mb-6">Mulai tambahkan akun game favorit Anda ke wishlist</p>
                <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>
                    Jelajahi Akun
                </a>
            </div>
            <?php else: ?>
            <!-- Wishlist Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($wishlistItems as $account): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Account Image -->
                    <div class="aspect-w-16 aspect-h-9 relative">
                        <?php if (isset($account->images->main_image)): ?>
                            <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                 alt="<?php echo htmlspecialchars($account->account_name); ?>"
                                 class="w-full h-48 object-cover">
                        <?php else: ?>
                            <img src="../assets/images/no-image.png" 
                                 alt="No Image Available"
                                 class="w-full h-48 object-cover">
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <?php if ($account->status === 'available'): ?>
                            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Tersedia
                            </span>
                        <?php else: ?>
                            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                Terjual
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Account Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($account->account_name); ?>
                        </h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-server w-5"></i>
                                <span>Server: <?php echo htmlspecialchars($account->server_id); ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-star w-5"></i>
                                <span>Level: <?php echo $account->level; ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-trophy w-5"></i>
                                <span>Rank: <?php echo htmlspecialchars($account->rank); ?></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-blue-600">
                                Rp <?php echo number_format($account->price, 0, ',', '.'); ?>
                            </span>
                            <div class="flex space-x-2">
                                <a href="account_detail.php?id=<?php echo $account->_id; ?>" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                                <button onclick="removeFromWishlist('<?php echo $account->_id; ?>')"
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function removeFromWishlist(accountId) {
        if (confirm('Apakah Anda yakin ingin menghapus item ini dari wishlist?')) {
            fetch('api/remove_from_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    account_id: accountId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus item dari wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus item');
            });
        }
    }
    </script>
</body>
</html>