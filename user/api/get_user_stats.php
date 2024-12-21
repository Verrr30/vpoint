<?php
require_once '../../config/database.php';
session_start();

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Hitung total pesanan user
$totalOrders = $database->transactions->countDocuments([
    'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
]);

// Hitung total saldo user
$userBalance = $database->users->findOne(
    ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
    ['projection' => ['balance' => 1]]
);

$balance = $userBalance->balance ?? 0;

echo json_encode([
    'total_orders' => $totalOrders,
    'balance' => $balance
]);