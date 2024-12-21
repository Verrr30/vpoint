<?php
session_start();
require_once 'config/database.php';

// Cek jika user sudah login
if (isset($_SESSION['user_id'])) {
    // Redirect berdasarkan role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    } else {
        header('Location: user/dashboard.php');
        exit();
    }
}

// Ambil daftar akun game yang tersedia
$accounts = $database->accounts->find(['status' => 'available'])->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VPoint - Jual Beli Akun Game</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <img src="assets/images/logo.png" alt="VPoint Logo" class="logo">
                <h1>VPoint</h1>
            </div>
            <div class="navbar-menu">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Temukan Akun Game Impianmu</h1>
            <p>Platform jual beli akun game terpercaya</p>
        </div>
    </section>

    <!-- Available Accounts -->
    <section class="available-accounts">
        <div class="container">
            <h2>Akun Game Tersedia</h2>
            <div class="account-grid">
                <?php foreach ($accounts as $account): ?>
                <div class="account-card">
                    <div class="account-image">
                        <?php if (isset($account->images->main_image) && $account->images->main_image): ?>
                            <img src="uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                 alt="<?php echo htmlspecialchars($account->account_name); ?>">
                        <?php else: ?>
                            <img src="assets/images/no-image.png" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="account-info">
                        <h3><?php echo htmlspecialchars($account->account_name); ?></h3>
                        <p class="server-id">Server ID: <?php echo htmlspecialchars($account->server_id); ?></p>
                        <p class="level">Level: <?php echo $account->level; ?></p>
                        <p class="rank">Rank: <?php echo htmlspecialchars($account->rank); ?></p>
                        <p class="price">Rp <?php echo number_format($account->price, 0, ',', '.'); ?></p>
                        <a href="login.php" class="btn btn-primary">Login untuk Membeli</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> VPoint. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html> 