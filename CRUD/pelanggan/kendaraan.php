<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }
require_once '../../DB/koneksi.php';

$pelanggan_id = intval($_GET['pelanggan_id'] ?? 0);
$pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id = $pelanggan_id"));
if (!$pelanggan) { header("Location: index.php"); exit(); }

if (isset($_GET['hapus'])) {
    $kid = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM kendaraan WHERE id = $kid AND pelanggan_id = $pelanggan_id");
    header("Location: kendaraan.php?pelanggan_id=$pelanggan_id&msg=hapus");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_plat = mysqli_real_escape_string($koneksi, trim($_POST['no_plat']));
    $merek   = mysqli_real_escape_string($koneksi, trim($_POST['merek']));
    $tipe    = mysqli_real_escape_string($koneksi, trim($_POST['tipe']));
    $warna   = mysqli_real_escape_string($koneksi, trim($_POST['warna']));
    $edit_id = intval($_POST['edit_id'] ?? 0);

    if ($no_plat === '' || $merek === '') {
        $error = 'No. Plat dan Merek wajib diisi.';
    } elseif ($edit_id > 0) {
        mysqli_query($koneksi, "UPDATE kendaraan SET no_plat='$no_plat', merek='$merek', tipe='$tipe', warna='$warna' WHERE id=$edit_id AND pelanggan_id=$pelanggan_id");
        header("Location: kendaraan.php?pelanggan_id=$pelanggan_id&msg=edit");
        exit();
    } else {
        mysqli_query($koneksi, "INSERT INTO kendaraan (pelanggan_id, no_plat, merek, tipe, warna) VALUES ($pelanggan_id,'$no_plat','$merek','$tipe','$warna')");
        header("Location: kendaraan.php?pelanggan_id=$pelanggan_id&msg=tambah");
        exit();
    }
}

// Jika edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id=$eid AND pelanggan_id=$pelanggan_id"));
}

$kendaraan = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE pelanggan_id = $pelanggan_id ORDER BY merek");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kendaraan - <?= htmlspecialchars($pelanggan['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-truck"></i> Kendaraan: <span class="text-primary"><?= htmlspecialchars($pelanggan['nama']) ?></span></h4>
        <a href="index.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Kendaraan berhasil <?= $_GET['msg'] == 'tambah' ? 'ditambahkan' : ($_GET['msg'] == 'edit' ? 'diperbarui' : 'dihapus') ?>.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Form Tambah/Edit -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header <?= $edit_data ? 'bg-warning' : 'bg-primary text-white' ?>">
                    <h6 class="mb-0"><?= $edit_data ? 'Edit Kendaraan' : 'Tambah Kendaraan' ?></h6>
                </div>
                <div class="card-body">
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">
                        <div class="mb-2">
                            <label class="form-label">No. Plat <span class="text-danger">*</span></label>
                            <input type="text" name="no_plat" class="form-control" value="<?= htmlspecialchars($edit_data['no_plat'] ?? '') ?>" placeholder="cth: BP 1234 AB" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Merek <span class="text-danger">*</span></label>
                            <input type="text" name="merek" class="form-control" value="<?= htmlspecialchars($edit_data['merek'] ?? '') ?>" placeholder="cth: Toyota" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tipe</label>
                            <input type="text" name="tipe" class="form-control" value="<?= htmlspecialchars($edit_data['tipe'] ?? '') ?>" placeholder="cth: Avanza">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warna</label>
                            <input type="text" name="warna" class="form-control" value="<?= htmlspecialchars($edit_data['warna'] ?? '') ?>" placeholder="cth: Putih">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn <?= $edit_data ? 'btn-warning' : 'btn-primary' ?> btn-sm">
                                <i class="bi bi-save"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="kendaraan.php?pelanggan_id=<?= $pelanggan_id ?>" class="btn btn-secondary btn-sm">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Kendaraan -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>No. Plat</th>
                                <th>Merek</th>
                                <th>Tipe</th>
                                <th>Warna</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($kendaraan)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['no_plat']) ?></strong></td>
                                <td><?= htmlspecialchars($row['merek']) ?></td>
                                <td><?= htmlspecialchars($row['tipe'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['warna'] ?? '-') ?></td>
                                <td>
                                    <a href="kendaraan.php?pelanggan_id=<?= $pelanggan_id ?>&edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <a href="kendaraan.php?pelanggan_id=<?= $pelanggan_id ?>&hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kendaraan ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
