<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

try {
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }

    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['avatar']['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    // Generate unique filename
    $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = '../uploads/avatars/';
    $avatar_path = $upload_path . $new_filename;

    // Create directory if it doesn't exist
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Update user's avatar in database
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => ['avatar' => $new_filename]]
    );

    if ($result->getModifiedCount() === 0) {
        // Delete uploaded file if database update fails
        unlink($avatar_path);
        throw new Exception('Failed to update user profile');
    }

    // Delete old avatar if exists
    $user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if (isset($user->avatar) && $user->avatar !== $new_filename) {
        $old_avatar = $upload_path . $user->avatar;
        if (file_exists($old_avatar)) {
            unlink($old_avatar);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully',
        'avatar_url' => '/vpoint/uploads/avatars/' . $new_filename
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>