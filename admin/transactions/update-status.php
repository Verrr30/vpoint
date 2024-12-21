<?php
require_once '../../config/database.php';
session_start();

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $transaction_id = $_POST['transaction_id'];
        $new_status = $_POST['status'];

        // Update status transaksi
        $result = $database->transactions->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($transaction_id)],
            ['$set' => [
                'status' => $new_status,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );

        // Jika completed, update status akun menjadi sold
        if ($new_status === 'completed') {
            $transaction = $database->transactions->findOne(['_id' => new MongoDB\BSON\ObjectId($transaction_id)]);
            if ($transaction) {
                $database->accounts->updateOne(
                    ['_id' => $transaction->account_id],
                    ['$set' => ['status' => 'sold']]
                );
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
