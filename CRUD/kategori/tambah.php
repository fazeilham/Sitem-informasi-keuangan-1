<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';
require_once '../../helpers.php';
if (!is_admin()) {
    header("Location: ../../index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $jenis = $_POST['jenis'];
    if ($nama === '') {
        $error = 'Nama kategori tidak boleh kosong.';
    } else {
        mysqli_query($koneksi, "INSERT INTO kategori (nama, jenis) VALUES ('$nama', '$jenis')");
        header("Location: index.php?msg=tambah");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:500px">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" placeholder="cth: Servis, Beli Oli..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select" required>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
