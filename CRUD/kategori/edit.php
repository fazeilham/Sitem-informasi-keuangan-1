<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

$id = intval($_GET['id'] ?? 0);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kategori WHERE id = $id"));
if (!$data) { header("Location: index.php"); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $jenis = $_POST['jenis'];
    if ($nama === '') {
        $error = 'Nama kategori tidak boleh kosong.';
    } else {
        mysqli_query($koneksi, "UPDATE kategori SET nama='$nama', jenis='$jenis' WHERE id=$id");
        header("Location: index.php?msg=edit");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:500px">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Kategori</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" class="form-select">
                        <option value="pemasukan" <?= $data['jenis']=='pemasukan'?'selected':'' ?>>Pemasukan</option>
                        <option value="pengeluaran" <?= $data['jenis']=='pengeluaran'?'selected':'' ?>>Pengeluaran</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
