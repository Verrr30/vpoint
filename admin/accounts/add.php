<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/AccountController.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$accountController = new AccountController($database);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $accountController->addAccount($_POST);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun Game - VPoint Admin</title>
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
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
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
                                        Tambah Akun Game
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
                        <?php if ($error): ?>
                            <div class="rounded-md bg-red-50 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800"><?php echo $error; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="rounded-md bg-green-50 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800"><?php echo $success; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Add Form -->
                        <div class="bg-white shadow rounded-lg">
                            <form method="POST" class="space-y-6 p-6" enctype="multipart/form-data">
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                    <div>
                                        <label for="account_name" class="block text-sm font-medium text-gray-700">Nama Akun</label>
                                        <input type="text" id="account_name" name="account_name" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="server_id" class="block text-sm font-medium text-gray-700">Server ID</label>
                                        <input type="text" id="server_id" name="server_id" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                                        <input type="number" id="level" name="level" min="1" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="rank" class="block text-sm font-medium text-gray-700">Rank</label>
                                        <input type="text" id="rank" name="rank" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
                                        <input type="number" id="price" name="price" min="0" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="winrate" class="block text-sm font-medium text-gray-700">Winrate (%)</label>
                                        <input type="number" id="winrate" name="winrate" step="0.01" min="0" max="100" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="total_matches" class="block text-sm font-medium text-gray-700">Total Match</label>
                                        <input type="number" id="total_matches" name="total_matches" min="0" required
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>

                                <!-- Emblem Status Section -->
                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Emblem</h3>
                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                                        <?php 
                                        $emblems = ['physical', 'magic', 'tank', 'assassin', 'support', 'fighter'];
                                        foreach ($emblems as $emblem): 
                                        ?>
                                        <div>
                                            <label for="emblem_<?php echo $emblem; ?>" class="block text-sm font-medium text-gray-700">
                                                <?php echo ucfirst($emblem); ?>
                                            </label>
                                            <input type="number" 
                                                   id="emblem_<?php echo $emblem; ?>" 
                                                   name="emblem_<?php echo $emblem; ?>" 
                                                   min="0" max="60"
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Image Upload Section -->
                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Akun</h3>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Gambar Utama</label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="main_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>Upload a file</span>
                                                            <input id="main_image" name="main_image" type="file" class="sr-only" accept="image/*" required>
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                                </div>
                                            </div>
                                            <div id="main_image_preview" class="mt-2 image-preview hidden"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Gambar Tambahan</label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="additional_images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>Upload multiple files</span>
                                                            <input id="additional_images" name="additional_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB each</p>
                                                </div>
                                            </div>
                                            <div id="additional_images_preview" class="mt-2 image-preview-grid"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-5">
                                    <div class="flex justify-end">
                                        <button type="submit" 
                                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
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
        function handleImagePreview(input, previewElement) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewElement.innerHTML = '';
                    previewElement.appendChild(img);
                    previewElement.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleMultipleImagePreview(input, previewElement) {
            if (input.files) {
                previewElement.innerHTML = '';
                
                for (let i = 0; i < input.files.length; i++) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'image-preview';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        div.appendChild(img);
                        previewElement.appendChild(div);
                    }
                    
                    reader.readAsDataURL(input.files[i]);
                }
            }
        }

        // Add event listeners for image preview
        document.getElementById('main_image').addEventListener('change', function() {
            handleImagePreview(this, document.getElementById('main_image_preview'));
        });

        document.getElementById('additional_images').addEventListener('change', function() {
            handleMultipleImagePreview(this, document.getElementById('additional_images_preview'));
        });
    </script>
</body>
</html>