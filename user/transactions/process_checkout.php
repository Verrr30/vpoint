<?php
session_start();
require_once '../../config/database.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../../login.php');
    exit();
}

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit();
}

// Validasi input
if (!isset($_POST['account_id']) || !isset($_POST['payment_method'])) {
    $_SESSION['error'] = 'Data tidak lengkap';
    header('Location: ../dashboard.php');
    exit();
}

try {
    // Ambil data akun
    $account = $database->accounts->findOne([
        '_id' => new MongoDB\BSON\ObjectId($_POST['account_id']),
        'status' => 'available'
    ]);

    if (!$account) {
        throw new Exception('Akun tidak tersedia');
    }

    // Start transaction
    $session = $database->startSession();
    $session->startTransaction();

    try {
        // Update status akun menjadi pending
        $database->accounts->updateOne(
            ['_id' => $account->_id],
            ['$set' => ['status' => 'pending']],
            ['session' => $session]
        );

        // Buat transaksi baru
        $transaction = [
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
            'account_id' => $account->_id,
            'transaction_date' => new MongoDB\BSON\UTCDateTime(),
            'status' => 'pending',
            'payment_details' => [
                'method' => $_POST['payment_method'],
                'amount' => (int)$account->price,
                'payment_status' => 'pending',
                'payment_proof' => null
            ]
        ];

        $result = $database->transactions->insertOne($transaction, ['session' => $session]);

        if (!$result->getInsertedCount()) {
            throw new Exception('Gagal membuat transaksi');
        }

        // Commit transaction
        $session->commitTransaction();

        // Redirect ke halaman upload bukti pembayaran
        $_SESSION['success'] = 'Transaksi berhasil dibuat';
        header('Location: upload_payment.php?id=' . $result->getInsertedId());
        exit();

    } catch (Exception $e) {
        // Rollback jika terjadi error
        $session->abortTransaction();
        throw $e;
    }

} catch (Exception $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header('Location: ../dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Checkout - VPoint</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/user.css">
</head>
<body>
    <div class="processing-container">
        <div class="loading-spinner"></div>
        <h2>Memproses Transaksi</h2>
        <p>Mohon tunggu sebentar...</p>
    </div>
</body>
</html> 