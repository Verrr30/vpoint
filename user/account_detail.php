<?php
session_start();
require_once '../config/database.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

// Cek ID akun
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data akun
$account = $database->accounts->findOne([
    '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
]);

// Jika akun tidak ditemukan
if (!$account) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data user
$user = $database->users->findOne([
    '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Akun - VPoint</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="user-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'includes/topbar.php'; ?>

            <div class="content">
                <div class="detail-container">
                    <div class="content-header">
                        <h2>Detail Akun</h2>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="account-detail">
                        <div class="account-images">
                            <div class="main-image">
                                <?php if (isset($account->images->main_image)): ?>
                                    <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $account->images->main_image; ?>" 
                                         alt="<?php echo htmlspecialchars($account->account_name); ?>"
                                         id="mainImage">
                                <?php else: ?>
                                    <img src="../assets/images/no-image.png" alt="No Image" id="mainImage">
                                <?php endif; ?>
                            </div>
                            <?php if (isset($account->images->gallery) && !empty($account->images->gallery)): ?>
                            <div class="image-gallery">
                                <?php foreach ($account->images->gallery as $image): ?>
                                    <img src="../uploads/accounts/<?php echo $account->_id; ?>/<?php echo $image; ?>" 
                                         alt="Gallery Image"
                                         onclick="changeMainImage(this.src)">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="account-info-detail">
                            <h1><?php echo htmlspecialchars($account->account_name); ?></h1>
                            
                            <div class="price-section">
                                <span class="price">Rp <?php echo number_format($account->price, 0, ',', '.'); ?></span>
                                <?php if ($account->status === 'available'): ?>
                                    <span class="status available">Tersedia</span>
                                <?php else: ?>
                                    <span class="status sold">Terjual</span>
                                <?php endif; ?>
                            </div>

                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fas fa-server"></i>
                                    <div>
                                        <label>Server ID</label>
                                        <span><?php echo htmlspecialchars($account->server_id); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-star"></i>
                                    <div>
                                        <label>Level</label>
                                        <span><?php echo $account->level; ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-trophy"></i>
                                    <div>
                                        <label>Rank</label>
                                        <span><?php echo htmlspecialchars($account->rank); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <div>
                                        <label>Tanggal Dibuat</label>
                                        <span><?php echo date('d M Y', $account->created_at->toDateTime()->getTimestamp()); ?></span>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($account->description)): ?>
                            <div class="description">
                                <h3>Deskripsi</h3>
                                <p><?php echo nl2br(htmlspecialchars($account->description)); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="action-buttons">
                                <?php if ($account->status === 'available'): ?>
                                    <a href="transactions/checkout.php?id=<?php echo $account->_id; ?>" class="btn btn-primary btn-large">
                                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                                    </a>
                                    <button class="btn btn-outline wishlist-btn" data-id="<?php echo $account->_id; ?>">
                                        <i class="far fa-heart"></i> Tambah ke Wishlist
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>Akun Tidak Tersedia</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // Wishlist functionality
    document.querySelector('.wishlist-btn')?.addEventListener('click', function() {
        const accountId = this.dataset.id;
        // Add your wishlist logic here
        this.innerHTML = '<i class="fas fa-heart"></i> Ditambahkan ke Wishlist';
        this.classList.add('added');
    });
    </script>

    <script src="../assets/js/user.js"></script>
</body>
</html> 