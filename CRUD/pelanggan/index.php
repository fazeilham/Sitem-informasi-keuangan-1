<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id = $id");
    header("Location: index.php?msg=hapus");
    exit();
}

$pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-people"></i> Master Pelanggan</h4>
        <div>
            <a href="../../index.php" class="btn btn-secondary btn-sm me-1"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <a href="tambah.php" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Tambah Pelanggan</a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_GET['msg'] == 'tambah' ? 'Pelanggan berhasil ditambahkan.' : ($_GET['msg'] == 'edit' ? 'Pelanggan berhasil diperbarui.' : 'Pelanggan berhasil dihapus.') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($pelanggan)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['telepon'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                        <td>
                            <a href="kendaraan.php?pelanggan_id=<?= $row['id'] ?>" class="btn btn-info btn-sm text-white"><i class="bi bi-truck"></i> Kendaraan</a>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                            <a href="index.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus pelanggan ini?')"><i class="bi bi-trash"></i> Hapus</a>
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
