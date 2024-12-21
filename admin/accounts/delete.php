<?php
require_once '../../config/database.php';
session_start();

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if (isset($_GET['id'])) {
    try {
        // Ambil data akun untuk hapus gambar
        $account = $database->accounts->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        
        // Hapus gambar dari storage
        if ($account && isset($account->images)) {
            foreach ($account->images as $image) {
                $imagePath = '../../assets/images/accounts/' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        // Hapus data dari database
        $result = $database->accounts->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        
        if ($result->getDeletedCount() > 0) {
            $_SESSION['success'] = 'Akun berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus akun!';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
}

header('Location: index.php');
exit();
