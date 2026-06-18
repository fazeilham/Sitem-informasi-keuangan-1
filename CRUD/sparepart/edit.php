<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

$id = intval($_GET['id'] ?? 0);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM sparepart WHERE id = $id"));
if (!$data) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kode_part  = mysqli_real_escape_string($koneksi, trim($_POST['kode_part']));
    $satuan     = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));
    $harga_beli = floatval($_POST['harga_beli']);
    $harga_jual = floatval($_POST['harga_jual']);
    $stok       = intval($_POST['stok']);

    if ($nama === '') { $error = 'Nama tidak boleh kosong.'; }
    else {
        mysqli_query($koneksi, "UPDATE sparepart SET nama='$nama', kode_part='$kode_part', satuan='$satuan', harga_beli=$harga_beli, harga_jual=$harga_jual, stok=$stok WHERE id=$id");
        header("Location: index.php?msg=edit");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:560px">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Edit Sparepart</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Sparepart</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Part</label>
                        <input type="text" name="kode_part" class="form-control" value="<?= htmlspecialchars($data['kode_part'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan'] ?? '') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="harga_beli" class="form-control" value="<?= $data['harga_beli'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="harga_jual" class="form-control" value="<?= $data['harga_jual'] ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $data['stok'] ?>">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
