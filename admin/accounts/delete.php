<?php
require_once '../../config/database.php';
session_start();

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Jika tidak ada ID, kembali ke halaman index
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$account = null;

try {
    // Ambil data akun
    $account = $database->accounts->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
    
    if (!$account) {
        $_SESSION['error'] = 'Akun tidak ditemukan.';
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header('Location: index.php');
    exit();
}

// Handle POST request untuk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Hapus gambar dari storage jika ada
        if (isset($account->images) && isset($account->images->main_image)) {
            $mainImagePath = "../../uploads/accounts/{$account->_id}/{$account->images->main_image}";
            if (file_exists($mainImagePath)) {
                unlink($mainImagePath);
            }
            
            // Hapus folder akun jika ada
            $accountDir = "../../uploads/accounts/{$account->_id}";
            if (is_dir($accountDir)) {
                rmdir($accountDir);
            }
        }

        // Hapus data dari database
        $result = $database->accounts->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        
        if ($result->getDeletedCount() > 0) {
            $_SESSION['success'] = 'Akun berhasil dihapus!';
            header('Location: index.php');
            exit();
        } else {
            $error = 'Gagal menghapus akun.';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Akun - VPoint Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Konfirmasi Penghapusan
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus akun ini?
                </p>
            </div>

            <?php if ($error): ?>
            <div class="rounded-md bg-red-50 p-4">
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

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <?php if (isset($account->images->main_image)): ?>
                            <img src="/vpoint/uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                 alt="<?php echo htmlspecialchars($account->account_name); ?>"
                                 class="w-20 h-20 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                <?php echo htmlspecialchars($account->account_name); ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                Server ID: <?php echo htmlspecialchars($account->server_id); ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                Level: <?php echo $account->level; ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end space-x-4">
                        <a href="index.php" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </a>
                        <form method="POST" class="inline-block">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>