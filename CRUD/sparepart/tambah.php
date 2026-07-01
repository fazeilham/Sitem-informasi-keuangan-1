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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama       = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $kode_part  = isset($_POST['kode_part']) ? mysqli_real_escape_string($koneksi, trim($_POST['kode_part'])) : '';
    $satuan     = isset($_POST['satuan']) ? mysqli_real_escape_string($koneksi, trim($_POST['satuan'])) : '';
    $harga_beli = isset($_POST['harga_beli']) ? floatval(str_replace('.', '', $_POST['harga_beli'])) : 0;
    $harga_jual = isset($_POST['harga_jual']) ? floatval(str_replace('.', '', $_POST['harga_jual'])) : 0;
    $stok       = isset($_POST['stok']) ? intval($_POST['stok']) : 0;

    if ($nama === '') {
        $error = 'Nama sparepart tidak boleh kosong.';
    } else {
        // Siapkan kolom dan nilai yang akan diinsert
        $insert_columns = ['nama'];
        $insert_values = ["'$nama'"];
        
        if (in_array('kode_part', $available_columns)) {
            $insert_columns[] = 'kode_part';
            $insert_values[] = "'$kode_part'";
        }
        
        if (in_array('satuan', $available_columns)) {
            $insert_columns[] = 'satuan';
            $insert_values[] = "'$satuan'";
        }
        
        if (in_array('harga_beli', $available_columns)) {
            $insert_columns[] = 'harga_beli';
            $insert_values[] = $harga_beli;
        }
        
        if (in_array('harga_jual', $available_columns)) {
            $insert_columns[] = 'harga_jual';
            $insert_values[] = $harga_jual;
        }
        
        if (in_array('stok', $available_columns)) {
            $insert_columns[] = 'stok';
            $insert_values[] = $stok;
        }
        
        $query = "INSERT INTO sparepart (" . implode(',', $insert_columns) . ") 
                  VALUES (" . implode(',', $insert_values) . ")";
        
        $result = mysqli_query($koneksi, $query);
        
        if ($result) {
            header("Location: index.php?msg=tambah");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($koneksi);
        }
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
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Sparepart <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="cth: Oli Mesin 10W-40"
                            required>
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