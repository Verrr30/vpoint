<div class="sidebar">
    <div class="sidebar-header">
        <h2>VPoint Admin</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="../dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/accounts/') !== false ? 'active' : ''; ?>">
                <a href="../accounts/">
                    <i class="fas fa-gamepad"></i> Akun Game
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/transactions/') !== false ? 'active' : ''; ?>">
                <a href="../transactions/">
                    <i class="fas fa-exchange-alt"></i> Transaksi
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : ''; ?>">
                <a href="../users/">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li>
                <a href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</div>