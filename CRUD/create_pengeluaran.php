<?php
session_start();
require_once '../DB/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';
$selected_tanggal = date('Y-m-d');
$selected_pelanggan = '';
$selected_kendaraan = '';
$selected_sparepart = '';
$selected_kategori = '';
$selected_metode_pembayaran = '';
$selected_jasa_detail = '';
$selected_nominal = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $pelanggan = mysqli_real_escape_string($koneksi, $_POST['pelanggan']);
    $kendaraan = mysqli_real_escape_string($koneksi, $_POST['kendaraan']);
    $sparepart = mysqli_real_escape_string($koneksi, $_POST['sparepart']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $jasa_detail = isset($_POST['jasa_detail']) ? mysqli_real_escape_string($koneksi, $_POST['jasa_detail']) : '';
    $nominal = isset($_POST['nominal']) ? mysqli_real_escape_string($koneksi, $_POST['nominal']) : '0';
    $user_id = $_SESSION['user_id'];

    $selected_tanggal = $tanggal;
    $selected_pelanggan = $pelanggan;
    $selected_kendaraan = $kendaraan;
    $selected_sparepart = $sparepart;
    $selected_kategori = $kategori;
    $selected_metode_pembayaran = $metode_pembayaran;
    $selected_jasa_detail = $jasa_detail;
    $selected_nominal = $nominal;

    if (empty($tanggal) || empty($kategori) || empty($nominal)) {
        $error = "Tanggal, Kategori, dan Nominal wajib diisi!";
    } else {
        $keterangan_full = "Kategori: " . $kategori;
        if (!empty($pelanggan)) $keterangan_full .= " | Pelanggan: " . $pelanggan;
        if (!empty($kendaraan)) $keterangan_full .= " | Kendaraan: " . $kendaraan;
        if (!empty($sparepart)) $keterangan_full .= " | Sparepart: " . $sparepart;
        if (!empty($jasa_detail)) $keterangan_full .= " | Keterangan: " . $jasa_detail;
        if (!empty($metode_pembayaran)) $keterangan_full .= " | Metode: " . $metode_pembayaran;

        $query = "INSERT INTO transaksi (tanggal, jenis, kategori, keterangan, unit_keterangan, jasa_detail, barang_sparepart, jumlah, user_id) 
                  VALUES ('$tanggal', 'pengeluaran', '$kategori', '$keterangan_full', '$kendaraan', '$jasa_detail', '$sparepart', '$nominal', '$user_id')";

        if (mysqli_query($koneksi, $query)) {
            $success = "Data pengeluaran berhasil ditambahkan!";
            $selected_tanggal = date('Y-m-d');
            $selected_pelanggan = ''; $selected_kendaraan = ''; $selected_sparepart = '';
            $selected_kategori = ''; $selected_metode_pembayaran = '';
            $selected_jasa_detail = ''; $selected_nominal = '';
            header("refresh:2;url=create_pengeluaran.php");
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengeluaran - Bengkel Biyai Racing Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #eb3349;
        --secondary-color: #f45c43;
    }

    body {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
        background: rgba(26, 26, 26, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    }

    .navbar-brand {
        font-weight: bold;
        color: #ffc107 !important;
        font-size: 1.5rem;
    }

    .navbar-brand i {
        color: #dc3545;
    }

    .main-container {
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        padding: 40px;
        margin-bottom: 30px;
    }

    .form-header {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 15px 15px 0 0;
        margin: -40px -40px 30px -40px;
    }

    .form-header h3 {
        margin: 0;
        font-weight: bold;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #eb3349;
        box-shadow: 0 0 0 0.2rem rgba(235, 51, 73, 0.25);
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .btn-submit {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(235, 51, 73, 0.4);
        color: white;
    }

    .btn-cancel {
        background: #6c757d;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(108, 117, 125, 0.4);
        color: white;
    }

    .required {
        color: #dc3545;
    }

    .alert {
        border-radius: 10px;
    }

    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }

    .form-control.with-icon {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-speedometer2"></i> Bengkel Biyai Racing Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-plus-circle"></i> Tambah Transaksi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="create_pemasukan.php">Tambah Pemasukan</a></li>
                            <li><a class="dropdown-item active" href="create_pengeluaran.php">Tambah Pengeluaran</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lihat.php">
                            <i class="bi bi-list-ul"></i> Lihat Semua
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../laporan/laporan_harian.php"><i class="bi bi-calendar-day"></i> Laporan Harian</a></li>
                            <li><a class="dropdown-item" href="../laporan/laporan_mingguan.php"><i class="bi bi-calendar-week"></i> Laporan Mingguan</a></li>
                            <li><a class="dropdown-item" href="../laporan/laporan_bulanan.php"><i class="bi bi-calendar-month"></i> Laporan Bulanan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database"></i> Master Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="kategori/index.php"><i class="bi bi-tags"></i> Kategori</a></li>
                            <li><a class="dropdown-item" href="pelanggan/index.php"><i class="bi bi-people"></i> Pelanggan</a></li>
                            <li><a class="dropdown-item" href="sparepart/index.php"><i class="bi bi-gear"></i> Sparepart</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../about.php">
                            <i class="bi bi-info-circle"></i> Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="form-card">
            <div class="form-header">
                <h3><i class="bi bi-arrow-up-circle"></i> Tambah Pengeluaran</h3>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal Transaksi <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" class="form-control with-icon" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($selected_tanggal); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                            <select class="form-select with-icon" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Cash" <?php echo $selected_metode_pembayaran == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                <option value="Transfer" <?php echo $selected_metode_pembayaran == 'Transfer' ? 'selected' : ''; ?>>Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori Transaksi <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tags"></i></span>
                            <input type="text" class="form-control with-icon" id="kategori" name="kategori" value="<?php echo htmlspecialchars($selected_kategori); ?>" placeholder="Contoh: Pembelian Sparepart, Gaji Karyawan" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sparepart" class="form-label">Sparepart / Barang</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <input type="text" class="form-control with-icon" id="sparepart" name="sparepart" value="<?php echo htmlspecialchars($selected_sparepart); ?>" placeholder="Nama Barang">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pelanggan" class="form-label">Pihak Tujuan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <input type="text" class="form-control with-icon" id="pelanggan" name="pelanggan" value="<?php echo htmlspecialchars($selected_pelanggan); ?>" placeholder="Nama Pihak Tujuan">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kendaraan" class="form-label">Keterangan Tambahan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                            <input type="text" class="form-control with-icon" id="kendaraan" name="kendaraan" value="<?php echo htmlspecialchars($selected_kendaraan); ?>" placeholder="Keterangan Lainnya">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jasa_detail" class="form-label"><i class="bi bi-pencil-square"></i> Keterangan Detail</label>
                    <textarea class="form-control" id="jasa_detail" name="jasa_detail" rows="3" placeholder="Detail pengeluaran"><?php echo htmlspecialchars($selected_jasa_detail); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="nominal" class="form-label">Nominal (Total) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-currency-exchange"></i> Rp</span>
                        <input type="number" class="form-control" id="nominal" name="nominal" placeholder="0" min="0" step="0.01" value="<?php echo htmlspecialchars($selected_nominal); ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="../index.php" class="btn btn-cancel">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle"></i> Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>