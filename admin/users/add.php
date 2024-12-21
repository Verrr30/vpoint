<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($database);

// Cek login admin
if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->register([
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'role' => $_POST['role']
    ]);

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: index.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - VPoint Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include '../includes/topbar.php'; ?>

            <div class="content-wrapper">
                <div class="content-header">
                    <h1>Tambah User Baru</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="form-grid" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       required 
                                       pattern="[a-zA-Z0-9_]{3,20}"
                                       title="Username hanya boleh mengandung huruf, angka, dan underscore. Panjang 3-20 karakter">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       minlength="6">
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;

        // Validasi password
        if (password.length < 6) {
            alert('Password harus minimal 6 karakter!');
            return false;
        }

        // Validasi username
        if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
            alert('Username tidak valid! Gunakan huruf, angka, dan underscore (3-20 karakter)');
            return false;
        }

        // Validasi email
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            alert('Format email tidak valid!');
            return false;
        }

        return true;
    }
    </script>
    <script src="../../assets/js/admin.js"></script>
</body>
</html> 