<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Account ID is required']);
    exit();
}

require_once '../../config/database.php';

try {
    // Remove from wishlist
    $result = $database->wishlist->deleteOne([
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
        'account_id' => new MongoDB\BSON\ObjectId($data['account_id'])
    ]);

    if ($result->getDeletedCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Item successfully removed from wishlist'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Item not found in wishlist'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error removing item from wishlist'
    ]);
}
?>