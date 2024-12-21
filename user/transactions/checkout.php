<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

// Cek login
requireLogin();

// Ambil ID akun dari parameter URL
$account_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$account_id) {
    header('Location: ../dashboard.php');
    exit();
}

// Ambil detail akun dari database
try {
    $account = $database->accounts->findOne([
        '_id' => new MongoDB\BSON\ObjectId($account_id),
        'status' => 'available'
    ]);

    if (!$account) {
        throw new Exception("Akun tidak ditemukan atau tidak tersedia.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
        
        // Upload bukti pembayaran
        $payment_proof = '';
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['payment_proof']['tmp_name'];
            $file_name = time() . '_' . $_FILES['payment_proof']['name'];
            $file_destination = '../../assets/images/payment-proof/' . $file_name;
            
            if (move_uploaded_file($file_tmp, $file_destination)) {
                $payment_proof = $file_name;
            }
        }
        
        // Persiapkan dokumen transaksi
        $document = [
            'user_id' => $user_id,
            'account_id' => new MongoDB\BSON\ObjectId($account_id),
            'transaction_date' => new MongoDB\BSON\UTCDateTime(),
            'status' => 'pending',
            'payment_details' => [
                'method' => 'bank_transfer',
                'amount' => (int)$account->price,
                'payment_status' => 'pending',
                'payment_proof' => $payment_proof
            ],
            'transaction_type' => 'account_purchase'
        ];
        
        // Insert ke MongoDB
        $result = $database->transactions->insertOne($document);
        
        if ($result->getInsertedId()) {
            // Update status akun menjadi pending
            $database->accounts->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($account_id)],
                ['$set' => ['status' => 'pending']]
            );
            
            $_SESSION['success'] = "Transaksi berhasil dibuat! Silahkan tunggu konfirmasi admin.";
            header('Location: history.php');
            exit();
        } else {
            throw new Exception("Terjadi kesalahan saat membuat transaksi.");
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VPoint</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Checkout Akun Game</h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mx-8 mt-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="p-8">
            <!-- Game Account Details -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <h3 class="text-xl font-semibold text-gray-800 pb-4 mb-6 border-b border-gray-200">
                    Detail Akun
                </h3>
                
                <div class="text-center mb-6">
                    <?php if (isset($account->images->main_image) && $account->images->main_image): ?>
                        <img src="../../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                             alt="<?php echo htmlspecialchars($account->account_name); ?>"
                             class="rounded-xl shadow-md max-w-full h-auto mx-auto">
                    <?php else: ?>
                        <img src="../../assets/images/no-image.png" 
                             alt="No Image"
                             class="rounded-xl shadow-md max-w-full h-auto mx-auto">
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm text-gray-500 font-medium">Nama Akun</label>
                        <div class="text-gray-800 font-semibold">
                            <?php echo htmlspecialchars($account->account_name); ?>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm text-gray-500 font-medium">Server ID</label>
                        <div class="text-gray-800 font-semibold">
                            <?php echo htmlspecialchars($account->server_id); ?>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm text-gray-500 font-medium">Level</label>
                        <div class="text-gray-800 font-semibold">
                            <?php echo $account->level; ?>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm text-gray-500 font-medium">Rank</label>
                        <div class="inline-block px-4 py-1 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-500">
                            <?php echo ucfirst(htmlspecialchars($account->rank)); ?>
                        </div>
                    </div>
                    <div class="space-y-1 md:col-span-2 lg:col-span-1">
                        <label class="text-sm text-gray-500 font-medium">Harga</label>
                        <div class="text-blue-600 text-xl font-bold">
                            Rp <?php echo number_format($account->price, 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <!-- Payment Methods -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Metode Pembayaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- BCA -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-blue-500 transition-all duration-300 group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 flex items-center justify-center bg-white rounded-lg shadow-sm">
                                    <span class="text-2xl">🏦</span>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">BCA</div>
                                    <div class="text-sm text-gray-600">1234567890</div>
                                    <div class="text-xs text-gray-500">a.n. VPoint Official</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mandiri -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-blue-500 transition-all duration-300 group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 flex items-center justify-center bg-white rounded-lg shadow-sm">
                                    <span class="text-2xl">🏦</span>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">Mandiri</div>
                                    <div class="text-sm text-gray-600">0987654321</div>
                                    <div class="text-xs text-gray-500">a.n. VPoint Official</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- DANA -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-blue-500 transition-all duration-300 group">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 flex items-center justify-center bg-white rounded-lg shadow-sm">
                                    <span class="text-2xl">📱</span>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">DANA</div>
                                    <div class="text-sm text-gray-600">081234567890</div>
                                    <div class="text-xs text-gray-500">a.n. VPoint Official</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Upload Bukti Transfer</h3>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-blue-500 transition-all duration-300">
                        <label for="payment_proof" class="cursor-pointer">
                            <div class="mx-auto w-16 h-16 mb-4 flex items-center justify-center bg-blue-50 rounded-full">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <span class="text-gray-600 font-medium">Pilih file atau drag & drop</span>
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required class="hidden">
                        </label>
                        <p class="text-sm text-gray-500 mt-2">Format yang didukung: JPG, PNG, JPEG. Maksimal 2MB</p>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-red-50 rounded-xl p-6 mb-8">
                    <h3 class="text-xl font-semibold text-red-800 mb-4">Catatan Penting</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Pastikan nominal transfer sesuai dengan harga akun
                        </li>
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan bukti pembayaran sampai transaksi selesai
                        </li>
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Proses verifikasi membutuhkan waktu 1x24 jam kerja
                        </li>
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Akun akan otomatis dikunci selama proses pembayaran
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:-translate-y-0.5 transition-all duration-300">
                        Konfirmasi Pembayaran
                    </button>
                    <a href="../dashboard.php" class="w-full bg-gray-600 text-white font-semibold py-3 px-6 rounded-xl hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transform hover:-translate-y-0.5 transition-all duration-300 text-center">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>