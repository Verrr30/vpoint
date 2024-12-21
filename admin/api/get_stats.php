<?php
require_once '../../config/database.php';
session_start();

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Hitung total pesanan
$totalOrders = $database->transactions->countDocuments();

// Hitung total saldo (jumlah semua transaksi yang completed)
$totalBalance = $database->transactions->aggregate([
    [
        '$match' => [
            'status' => 'completed'
        ]
    ],
    [
        '$group' => [
            '_id' => null,
            'total' => ['$sum' => '$payment_details.amount']
        ]
    ]
])->toArray();

$balance = $totalBalance[0]->total ?? 0;

echo json_encode([
    'total_orders' => $totalOrders,
    'balance' => $balance
]);