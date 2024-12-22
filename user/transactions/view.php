<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /vpoint/login.php');
    exit();
}

// Get transaction ID from URL
$transaction_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$transaction_id) {
    header('Location: /vpoint/user/orders.php');
    exit();
}

try {
    // Get transaction details with account information
    $transaction = $database->transactions->findOne([
        '_id' => new MongoDB\BSON\ObjectId($transaction_id),
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
    ]);

    if (!$transaction) {
        $_SESSION['error'] = "Transaksi tidak ditemukan.";
        header('Location: /vpoint/user/orders.php');
        exit();
    }

    // Get account details
    $account = $database->accounts->findOne([
        '_id' => new MongoDB\BSON\ObjectId($transaction->account_id)
    ]);

} catch (Exception $e) {
    $_SESSION['error'] = "Error mengambil detail transaksi.";
    header('Location: /vpoint/user/orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - VPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include '../../includes/header.php'; ?>

    <div class="min-h-screen py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Detail Transaksi</h1>
                <a href="/vpoint/user/orders.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>

            <!-- Transaction Details Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <!-- Transaction ID -->
                    <div class="flex items-center py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">ID Transaksi</span>
                        </div>
                        <div class="w-2/3">
                            <span class="text-sm text-gray-900"><?php echo $transaction->_id; ?></span>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div class="flex items-start py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">Detail Akun</span>
                        </div>
                        <div class="w-2/3">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-4">
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
                                        <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($account->account_name); ?></h3>
                                        <p class="text-sm text-gray-500">Server ID: <?php echo htmlspecialchars($account->server_id); ?></p>
                                        <p class="text-sm text-gray-500">Level: <?php echo $account->level; ?></p>
                                        <p class="text-sm text-gray-500">Rank: <?php echo htmlspecialchars($account->rank); ?></p>
                                    </div>
                                </div>
                                <div class="text-lg font-semibold text-blue-600">
                                    Rp <?php echo number_format($account->price, 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">Status</span>
                        </div>
                        <div class="w-2/3">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800'
                            ];
                            $status = $transaction->status ?? 'pending';
                            $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                <?php 
                                $statusText = [
                                    'pending' => 'Menunggu Pembayaran',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan'
                                ];
                                echo $statusText[$status] ?? ucfirst($status); 
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Transaction Date -->
                    <div class="flex items-center py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">Tanggal Transaksi</span>
                        </div>
                        <div class="w-2/3">
                            <span class="text-sm text-gray-900">
                                <?php 
                                if (isset($transaction->created_at)) {
                                    if ($transaction->created_at instanceof MongoDB\BSON\UTCDateTime) {
                                        $date = $transaction->created_at->toDateTime();
                                        $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                        echo $date->format('d F Y, H:i');
                                    } else {
                                        echo date('d F Y, H:i', strtotime($transaction->created_at));
                                    }
                                } else {
                                    echo date('d F Y, H:i');
                                }
                                ?> WIB
                            </span>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <?php if (isset($transaction->payment_proof)): ?>
                    <div class="flex items-start py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">Bukti Pembayaran</span>
                        </div>
                        <div class="w-2/3">
                            <div class="mt-1">
                                <img src="/vpoint/uploads/payment_proof/<?php echo htmlspecialchars($transaction->payment_proof); ?>" 
                                     alt="Bukti Pembayaran"
                                     class="max-w-md rounded-lg shadow-sm cursor-pointer hover:shadow-md transition-shadow duration-200"
                                     onclick="openImageModal(this.src)">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <?php if ($status === 'pending'): ?>
                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="upload_payment.php?id=<?php echo $transaction->_id; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Bukti Pembayaran
                        </a>
                        <button onclick="cancelTransaction('<?php echo $transaction->_id; ?>')"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>
                            Batalkan Transaksi
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-75 flex items-center justify-center">
        <div class="relative">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-xl">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalImage" src="" alt="Full size image" class="max-w-screen-lg max-h-screen-lg">
        </div>
    </div>

    <script>
    function openImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.classList.remove('hidden');
        modalImg.src = src;
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function cancelTransaction(transactionId) {
        if (confirm('Apakah Anda yakin ingin membatalkan transaksi ini?')) {
            window.location.href = `cancel.php?id=${transactionId}`;
        }
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
    </script>
</body>
</html>