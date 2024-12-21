<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /vpoint/login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /vpoint/login.php');
        exit();
    }
}

// Fungsi untuk mendapatkan data user yang sedang login
function getCurrentUser() {
    global $database;
    if (isLoggedIn()) {
        $userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
        return $database->users->findOne(['_id' => $userId]);
    }
    return null;
}
