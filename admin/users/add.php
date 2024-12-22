<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($database);

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->register([
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'role' => $_POST['role']
    ]);

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: index.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - VPoint Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
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
                                <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
                                    Transaksi
                                </a>
                                <a href="../users/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                                    <i class="fas fa-users w-5 h-5 mr-3"></i>
                                    Users
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
                    <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
                        <div class="py-6 md:flex md:items-center md:justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                        Tambah User Baru
                                    </h1>
                                </div>
                            </div>
                            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                                <a href="index.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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

                        <!-- Form -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <form method="POST" class="space-y-6" onsubmit="return validateForm()">
                                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                        <div>
                                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                            <input type="text" 
                                                   id="username" 
                                                   name="username" 
                                                   required 
                                                   pattern="[a-zA-Z0-9_]{3,20}"
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                   title="Username hanya boleh mengandung huruf, angka, dan underscore. Panjang 3-20 karakter">
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" 
                                                   id="email" 
                                                   name="email" 
                                                   required
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>

                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                            <input type="password" 
                                                   id="password" 
                                                   name="password" 
                                                   required 
                                                   minlength="6"
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-sm text-gray-500">Minimal 6 karakter</p>
                                        </div>

                                        <div>
                                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                            <select id="role" 
                                                    name="role" 
                                                    required
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-save mr-2"></i>
                                            Simpan
                                        </button>
                                    </div>
                                </form>
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
                    <a href="../dashboard.php" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-home w-5 h-5 mr-3 text-gray-400"></i>
                        Dashboard
                    </a>
                    <a href="../accounts/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-gamepad w-5 h-5 mr-3 text-gray-400"></i>
                        Akun Game
                    </a>
                    <a href="../transactions/" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-exchange-alt w-5 h-5 mr-3 text-gray-400"></i>
                        Transaksi
                    </a>
                    <a href="../users/" class="flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        Users
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;

        // Validasi password
        if (password.length < 6) {
            alert('Password harus minimal 6 karakter!');
            return false;
        }

        // Validasi username
        if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
            alert('Username tidak valid! Gunakan huruf, angka, dan underscore (3-20 karakter)');
            return false;
        }

        // Validasi email
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            alert('Format email tidak valid!');
            return false;
        }

        return true;
    }

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