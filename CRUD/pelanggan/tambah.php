<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

$error = '';
$nama = '';
$no_hp = '';
$alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $no_hp = mysqli_real_escape_string($koneksi, trim($_POST['telepon']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));

    if ($nama === '') {
        $error = 'Nama pelanggan tidak boleh kosong.';
    } else {
        $insert_sql = "INSERT INTO pelanggan (nama, no_hp, alamat) VALUES ('$nama', '$no_hp', '$alamat')";
        if (mysqli_query($koneksi, $insert_sql)) {
            header("Location: index.php?msg=tambah");
            exit();
        }
        $error = 'Gagal menyimpan pelanggan: ' . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container py-4" style="max-width:520px">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-plus"></i> Tambah Pelanggan</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" placeholder="cth: 08123456789"
                            value="<?= htmlspecialchars($no_hp) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control"
                            rows="3"><?= htmlspecialchars($alamat) ?></textarea>
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