<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

// Dapatkan kolom yang ada di tabel sparepart
$available_columns = [];
$cols_result = mysqli_query($koneksi, "SHOW COLUMNS FROM sparepart");
if ($cols_result) {
    while ($row = mysqli_fetch_assoc($cols_result)) {
        $available_columns[] = $row['Field'];
    }
}

$id = intval($_GET['id'] ?? 0);
$data_query = mysqli_query($koneksi, "SELECT * FROM sparepart WHERE id = $id");
$data = mysqli_fetch_assoc($data_query);
if (!$data) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kode_part  = isset($_POST['kode_part']) ? mysqli_real_escape_string($koneksi, trim($_POST['kode_part'])) : '';
    $satuan     = isset($_POST['satuan']) ? mysqli_real_escape_string($koneksi, trim($_POST['satuan'])) : '';
    $harga_beli = isset($_POST['harga_beli']) ? floatval($_POST['harga_beli']) : 0;
    $harga_jual = isset($_POST['harga_jual']) ? floatval($_POST['harga_jual']) : 0;
    $stok       = isset($_POST['stok']) ? intval($_POST['stok']) : 0;

    if ($nama === '') { 
        $error = 'Nama tidak boleh kosong.'; 
    } else {
        // Siapkan set untuk update
        $update_sets = ["nama='$nama'"];
        
        if (in_array('kode_part', $available_columns)) {
            $update_sets[] = "kode_part='$kode_part'";
        }
        
        if (in_array('satuan', $available_columns)) {
            $update_sets[] = "satuan='$satuan'";
        }
        
        if (in_array('harga_beli', $available_columns)) {
            $update_sets[] = "harga_beli=$harga_beli";
        }
        
        if (in_array('harga_jual', $available_columns)) {
            $update_sets[] = "harga_jual=$harga_jual";
        }
        
        if (in_array('stok', $available_columns)) {
            $update_sets[] = "stok=$stok";
        }
        
        $query = "UPDATE sparepart SET " . implode(',', $update_sets) . " WHERE id=$id";
        $result = mysqli_query($koneksi, $query);
        
        if ($result) {
            header("Location: index.php?msg=edit");
            exit();
        } else {
            $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container py-4" style="max-width:560px">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Sparepart</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Sparepart</label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Part</label>
                            <input type="text" name="kode_part" class="form-control"
                                value="<?= htmlspecialchars($data['kode_part'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control"
                                value="<?= htmlspecialchars($data['satuan'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control"
                                value="<?= htmlspecialchars($data['harga_beli'] ?? 0) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" name="harga_jual" class="form-control"
                                value="<?= htmlspecialchars($data['harga_jual'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control"
                            value="<?= htmlspecialchars($data['stok'] ?? 0) ?>">
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