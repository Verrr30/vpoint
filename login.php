<?php
require_once 'config/database.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Cari user berdasarkan email
        $user = $database->users->findOne(['email' => $email]);

        if ($user) {
            // Debug: Cek password yang diinput dan hash yang tersimpan
            // echo "Input password: " . $password . "<br>";
            // echo "Stored hash: " . $user->password . "<br>";
            
            // Verifikasi password
            if (password_verify($password, $user->password)) {
                // Cek status user
                if ($user->status !== true) {
                    throw new Exception('Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
                }

                // Set session
                $_SESSION['user_id'] = (string)$user->_id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;

                // Update last_login
                $database->users->updateOne(
                    ['_id' => $user->_id],
                    ['$set' => [
                        'last_login' => new MongoDB\BSON\UTCDateTime(),
                        'updated_at' => new MongoDB\BSON\UTCDateTime()
                    ]]
                );

                // Redirect berdasarkan role
                if ($user->role === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                throw new Exception('Email atau password salah');
            }
        } else {
            throw new Exception('Email atau password salah');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VPoint</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>VPoint</h1>
                <p>Jual Beli Akun Mobile Legends</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
