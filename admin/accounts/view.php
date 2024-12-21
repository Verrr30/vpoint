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

$account = $database->accounts->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
if (!$account) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Akun - VPoint Admin</title>
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
                                <a href="../accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                                    <i class="fas fa-gamepad w-5 h-5 mr-3"></i>
                                    Akun Game
                                </a>
                                <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
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
                                        Detail Akun Game
                                    </h1>
                                    <div class="flex space-x-3">
                                        <a href="edit.php?id=<?php echo $account->_id; ?>" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit
                                        </a>
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
                </div>

                <div class="mt-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Basic Information -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nama Akun</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($account->account_name); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Server ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($account->server_id); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Level</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo $account->level; ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rank</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($account->rank); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Harga</dt>
                                        <dd class="mt-1 text-sm text-gray-900">Rp <?php echo number_format($account->price, 0, ',', '.'); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <?php
                                            $statusColors = [
                                                'available' => 'bg-green-100 text-green-800',
                                                'sold' => 'bg-gray-100 text-gray-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800'
                                            ];
                                            $statusColor = $statusColors[$account->status] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 <?php echo $statusColor; ?>">
                                                <?php echo ucfirst($account->status); ?>
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Statistics -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Statistik</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Winrate</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo $account->details->winrate; ?>%</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Total Match</dt>
                                        <dd class="mt-1 text-sm text-gray-900"><?php echo $account->details->total_matches; ?></dd>
                                    </div>
                                </dl>
                            </div>

                            <?php if (isset($account->images) && !empty($account->images)): ?>
                            <!-- Account Images -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card lg:col-span-2">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Gambar Akun</h2>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                    <?php foreach ($account->images as $image): ?>
                                    <div class="relative aspect-w-1 aspect-h-1 rounded-lg overflow-hidden">
                                        <img src="../../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $image; ?>" 
                                             alt="Account Image"
                                             class="w-full h-48 object-cover rounded-lg hover:opacity-75 transition-opacity cursor-pointer"
                                             onclick="showImagePreview(this.src)">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($account->heroes) && !empty($account->heroes)): ?>
                            <!-- Heroes -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card lg:col-span-2">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Heroes</h2>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <?php foreach ($account->heroes as $hero): ?>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($hero->hero_name); ?></h3>
                                        <dl class="mt-2 text-sm text-gray-500">
                                            <div class="flex justify-between">
                                                <dt>Level:</dt>
                                                <dd class="text-gray-900"><?php echo $hero->hero_level; ?></dd>
                                            </div>
                                            <div class="flex justify-between mt-1">
                                                <dt>Winrate:</dt>
                                                <dd class="text-gray-900"><?php echo $hero->winrate; ?>%</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($account->skins) && !empty($account->skins)): ?>
                            <!-- Skins -->
                            <div class="bg-white shadow rounded-lg p-6 detail-card lg:col-span-2">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Skins</h2>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <?php foreach ($account->skins as $skin): ?>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($skin->skin_name); ?></h3>
                                        <dl class="mt-2 text-sm text-gray-500">
                                            <div class="flex justify-between">
                                                <dt>Hero:</dt>
                                                <dd class="text-gray-900"><?php echo htmlspecialchars($skin->hero_name); ?></dd>
                                            </div>
                                            <div class="flex justify-between mt-1">
                                                <dt>Type:</dt>
                                                <dd class="text-gray-900"><?php echo htmlspecialchars($skin->skin_type); ?></dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
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
                    <a href="../accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <i class="fas fa-gamepad w-5 h-5 mr-3"></i>
                        Akun Game
                    </a>
                    <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
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

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <img id="previewImage" src="" alt="Preview" class="w-full h-auto">
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeImagePreview()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
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

        // Image preview functionality
        function showImagePreview(src) {
            const modal = document.getElementById('imagePreviewModal');
            const previewImage = document.getElementById('previewImage');
            previewImage.src = src;
            modal.classList.remove('hidden');
        }

        function closeImagePreview() {
            const modal = document.getElementById('imagePreviewModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('imagePreviewModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImagePreview();
            }
        });
    </script>
</body>
</html>