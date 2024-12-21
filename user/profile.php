<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /vpoint/login.php');
    exit();
}

try {
    // Get user data
    $user = $database->users->findOne([
        '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
    ]);

    if (!$user) {
        header('Location: /vpoint/login.php');
        exit();
    }

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $updates = [];
        
        // Handle basic info updates
        if (isset($_POST['username'])) {
            $updates['username'] = $_POST['username'];
        }
        if (isset($_POST['email'])) {
            $updates['email'] = $_POST['email'];
        }
        if (isset($_POST['phone'])) {
            $updates['phone'] = $_POST['phone'];
        }

        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar_tmp = $_FILES['avatar']['tmp_name'];
            $avatar_name = time() . '_' . $_FILES['avatar']['name'];
            $avatar_path = '../uploads/avatars/' . $avatar_name;

            if (move_uploaded_file($avatar_tmp, $avatar_path)) {
                $updates['avatar'] = $avatar_name;
            }
        }

        // Update user data
        if (!empty($updates)) {
            $database->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                ['$set' => $updates]
            );
            
            // Refresh user data
            $user = $database->users->findOne([
                '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
            ]);
            
            $_SESSION['success'] = 'Profile updated successfully!';
            header('Location: profile.php');
            exit();
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'An error occurred. Please try again.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - VPoint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="min-h-screen py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Profile Settings</h1>
            </div>

            <!-- Success Message -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Profile Form -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <form action="profile.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    <!-- Avatar Section -->
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <?php if (isset($user->avatar) && $user->avatar): ?>
                                <img class="h-16 w-16 object-cover rounded-full" 
                                     src="/vpoint/uploads/avatars/<?php echo htmlspecialchars($user->avatar); ?>" 
                                     alt="Current avatar">
                            <?php else: ?>
                                <img class="h-16 w-16 object-cover rounded-full" 
                                     src="/vpoint/assets/images/default-avatar.png" 
                                     alt="Default avatar">
                            <?php endif; ?>
                        </div>
                        <label class="block">
                            <span class="sr-only">Choose profile photo</span>
                            <input type="file" name="avatar" 
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100">
                        </label>
                    </div>

                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" 
                                   value="<?php echo htmlspecialchars($user->username ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" 
                                   value="<?php echo htmlspecialchars($user->email ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone" 
                                   value="<?php echo htmlspecialchars($user->phone ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="mt-6 bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Change Password</h2>
                    <form action="change_password.php" method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="new_password" id="new_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Preview avatar image before upload
    document.querySelector('input[name="avatar"]')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('img.h-16')?.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>