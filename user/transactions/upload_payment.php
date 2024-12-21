<?php

require_once '../../config/database.php';
require_once '../../includes/auth.php';

// Cek login
requireLogin();

// Ambil ID transaksi dari parameter URL
$transaction_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$transaction_id) {
    header('Location: ../orders.php');
    exit();
}

try {
    // Ambil detail transaksi
    $transaction = $database->transactions->findOne([
        '_id' => new MongoDB\BSON\ObjectId($transaction_id),
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
    ]);

    if (!$transaction) {
        throw new Exception("Transaksi tidak ditemukan.");
    }

    // Ambil detail akun
    $account = $database->accounts->findOne([
        '_id' => $transaction->account_id
    ]);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['payment_proof']['tmp_name'];
            $file_name = time() . '_' . $_FILES['payment_proof']['name'];
            $file_destination = '../../assets/images/payment-proof/' . $file_name;
            
            if (move_uploaded_file($file_tmp, $file_destination)) {
                // Update transaksi dengan bukti pembayaran baru
                $database->transactions->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($transaction_id)],
                    ['$set' => [
                        'payment_details.payment_proof' => $file_name,
                        'payment_details.upload_date' => new MongoDB\BSON\UTCDateTime()
                    ]]
                );
                
                $_SESSION['success'] = "Bukti pembayaran berhasil diupload!";
                header('Location: history.php');
                exit();
            } else {
                throw new Exception("Gagal mengupload file.");
            }
        } else {
            throw new Exception("Silakan pilih file bukti pembayaran.");
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran - VPoint</title>
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
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-8 py-6">
                        <h2 class="text-2xl font-semibold text-white">Upload Bukti Pembayaran</h2>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="mx-8 mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-8">
                        <!-- Transaction Details -->
                        <div class="mb-8 bg-gray-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Transaksi</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">ID Transaksi</p>
                                    <p class="font-medium"><?php echo substr($transaction_id, -8); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                                    <p class="font-medium">Rp <?php echo number_format($transaction->payment_details->amount, 0, ',', '.'); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Metode Pembayaran</p>
                                    <p class="font-medium"><?php echo ucwords(str_replace('_', ' ', $transaction->payment_details->method)); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu Pembayaran
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Instruksi Pembayaran</h3>
                            <div class="space-y-4">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl">🏦</span>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">Transfer Bank</h4>
                                            <p class="text-sm text-blue-700">BCA: 1234567890 a.n. VPoint Official</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-600">1. Transfer sesuai nominal yang tertera</p>
                                    <p class="text-sm text-gray-600">2. Simpan bukti transfer</p>
                                    <p class="text-sm text-gray-600">3. Upload bukti transfer di bawah ini</p>
                                    <p class="text-sm text-gray-600">4. Tunggu konfirmasi dari admin (1x24 jam)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti Pembayaran
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload file</span>
                                                <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept="image/*" required>
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, JPEG up to 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <a href="../orders.php" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Batal
                                </a>
                                <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Upload Bukti Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview image before upload
        const input = document.querySelector('input[type="file"]');
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'mt-4 mx-auto h-32 w-auto rounded-lg shadow-sm';
                    
                    const previewContainer = document.querySelector('.space-y-1');
                    const existingPreview = previewContainer.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    previewContainer.appendChild(preview);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
