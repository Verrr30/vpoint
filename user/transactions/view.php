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
    header('Location: /vpoint/user/dashboard.php');
    exit();
}

try {
    // Get transaction details
    $transaction = $database->transactions->findOne([
        '_id' => new MongoDB\BSON\ObjectId($transaction_id),
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
    ]);

    if (!$transaction) {
        $_SESSION['error'] = "Transaction not found.";
        header('Location: /vpoint/user/dashboard.php');
        exit();
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching transaction details.";
    header('Location: /vpoint/user/dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
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
                <a href="/vpoint/user/dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

                    <!-- Amount -->
                    <div class="flex items-center py-4 border-b border-gray-200">
                        <div class="w-1/3">
                            <span class="text-sm font-medium text-gray-500">Jumlah V-Point</span>
                        </div>
                        <div class="w-2/3">
                            <span class="text-sm font-semibold text-gray-900">
                                <?php echo number_format($transaction->amount); ?> VP
                            </span>
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
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $status = $transaction->status ?? 'pending';
                            $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                <?php echo ucfirst($status); ?>
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
                                $date = $transaction->created_at instanceof MongoDB\BSON\UTCDateTime 
                                    ? $transaction->created_at->toDateTime() 
                                    : new DateTime();
                                echo $date->format('d F Y, H:i'); 
                                ?> WIB
                            </span>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <?php if (isset($transaction->payment_proof)): ?>
                    <div class="flex items-start py-4">
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