<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';
require_once '../../helpers.php';
if (!is_admin()) {
    header("Location: ../../index.php");
    exit();
}

// Dapatkan kolom yang ada di tabel sparepart
$available_columns = [];
$cols_result = mysqli_query($koneksi, "SHOW COLUMNS FROM sparepart");
if ($cols_result) {
    while ($row = mysqli_fetch_assoc($cols_result)) {
        $available_columns[] = $row['Field'];
    }
}

// Hapus data jika diminta
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM sparepart WHERE id = $id");
    header("Location: index.php?msg=hapus");
    exit();
}

// Ambil data sparepart
$sparepart = mysqli_query($koneksi, "SELECT * FROM sparepart ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Master Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4><i class="bi bi-gear"></i> Master Sparepart</h4>
            <div>
                <a href="../../index.php" class="btn btn-secondary btn-sm me-1"><i class="bi bi-arrow-left"></i>
                    Dashboard</a>
                <a href="tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Tambah Sparepart</a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Sparepart berhasil
            <?= $_GET['msg'] == 'tambah' ? 'ditambahkan' : ($_GET['msg'] == 'edit' ? 'diperbarui' : 'dihapus') ?>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <?php if (in_array('satuan', $available_columns)): ?>
                            <th>Satuan</th>
                        <?php endif; ?>
                        <?php if (in_array('harga_beli', $available_columns)): ?>
                            <th>Harga Beli</th>
                        <?php endif; ?>
                        <?php if (in_array('harga_jual', $available_columns)): ?>
                            <th>Harga Jual</th>
                        <?php endif; ?>
                        <?php if (in_array('stok', $available_columns)): ?>
                            <th>Stok</th>
                        <?php endif; ?>
                        <th>Aksi</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($sparepart)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <?php if (in_array('satuan', $available_columns)): ?>
                                <td><?= htmlspecialchars($row['satuan'] ?? '-') ?></td>
                            <?php endif; ?>
                            <?php if (in_array('harga_beli', $available_columns)): ?>
                            <td>Rp <?= number_format($row['harga_beli'] ?? 0, 0, ',', '.') ?></td>
                            <?php endif; ?>
                            <?php if (in_array('harga_jual', $available_columns)): ?>
                            <td>Rp <?= number_format($row['harga_jual'] ?? 0, 0, ',', '.') ?></td>
                            <?php endif; ?>
                            <?php if (in_array('stok', $available_columns)): ?>
                            <td>
                                <span class="badge <?= ($row['stok'] ?? 0) <= 5 ? 'bg-danger' : 'bg-success' ?>">
                                    <?= $row['stok'] ?? 0 ?>
                                </span>
                            </td>
                            <?php endif; ?>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i
                                        class="bi bi-pencil"></i> Edit</a>
                                <a href="index.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus sparepart ini?')"><i class="bi bi-trash"></i>
                                    Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>