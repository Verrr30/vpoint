<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/header.php';

// Cek login
requireLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $account_name = filter_input(INPUT_POST, 'account_name', FILTER_SANITIZE_STRING);
        $server_id = filter_input(INPUT_POST, 'server_id', FILTER_SANITIZE_STRING);
        $level = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_NUMBER_INT);
        $rank = filter_input(INPUT_POST, 'rank', FILTER_SANITIZE_STRING);

        // Validasi input
        if (empty($account_name) || empty($server_id) || empty($level) || empty($rank)) {
            throw new Exception("Semua field harus diisi!");
        }

        // Persiapkan dokumen untuk MongoDB
        $document = [
            'account_name' => $account_name,
            'server_id' => $server_id,
            'level' => (int)$level,
            'rank' => $rank,
            'status' => 'available',
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
            'details' => new stdClass(),
            'images' => new stdClass()
        ];

        // Insert ke MongoDB
        $result = $database->accounts->insertOne($document);

        if ($result->getInsertedId()) {
            $_SESSION['success'] = "Akun game berhasil ditambahkan!";
            header('Location: index.php');
            exit();
        } else {
            throw new Exception("Gagal menambahkan akun game.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Daftar rank yang tersedia
$ranks = [
    'immortal' => 'Immortal',
    'divine' => 'Divine',
    'ancient' => 'Ancient',
    'legend' => 'Legend',
    'archon' => 'Archon',
    'crusader' => 'Crusader',
    'guardian' => 'Guardian',
    'herald' => 'Herald'
];
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Tambah Akun Game</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" 
                                   required placeholder="Masukkan nama akun game">
                            <div class="invalid-feedback">
                                Nama akun harus diisi
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="server_id" class="form-label">Server ID</label>
                            <input type="text" class="form-control" id="server_id" name="server_id" 
                                   required placeholder="Contoh: 12345678">
                            <div class="invalid-feedback">
                                Server ID harus diisi
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <input type="number" class="form-control" id="level" name="level" 
                                   required min="1" max="999" placeholder="Level akun">
                            <div class="invalid-feedback">
                                Level harus diisi (1-999)
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rank" class="form-label">Rank</label>
                            <select class="form-select" id="rank" name="rank" required>
                                <option value="">Pilih Rank</option>
                                <?php foreach ($ranks as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Pilih rank akun
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Tambah Akun</button>
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()
</script>

<?php require_once '../../includes/footer.php'; ?>
