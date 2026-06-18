<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kode_part  = mysqli_real_escape_string($koneksi, trim($_POST['kode_part']));
    $satuan     = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));
    $harga_beli = floatval(str_replace('.', '', $_POST['harga_beli']));
    $harga_jual = floatval(str_replace('.', '', $_POST['harga_jual']));
    $stok       = intval($_POST['stok']);

    if ($nama === '') {
        $error = 'Nama sparepart tidak boleh kosong.';
    } else {
        mysqli_query($koneksi, "INSERT INTO sparepart (nama, kode_part, satuan, harga_beli, harga_jual, stok)
            VALUES ('$nama','$kode_part','$satuan',$harga_beli,$harga_jual,$stok)");
        header("Location: index.php?msg=tambah");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:560px">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-gear"></i> Tambah Sparepart</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Sparepart <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="cth: Oli Mesin 10W-40" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Part</label>
                        <input type="text" name="kode_part" class="form-control" placeholder="cth: OLI-001">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="cth: liter, pcs, set">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Jual (Rp)</label>
                        <input type="number" name="harga_jual" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stok" class="form-control" value="0" min="0">
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
