<?php
session_start();
require_once '../DB/koneksi.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';
$selected_tanggal = date('Y-m-d');
$selected_jenis = '';
$selected_kategori = '';
$selected_pelanggan = '';
$selected_kendaraan = '';
$selected_sparepart = '';
$selected_qty = '';
$selected_harga_satuan = '';
$selected_subtotal = '';
$selected_jasa_detail = '';
$selected_nominal = '';

$kategori_list = mysqli_query($koneksi, "SELECT id, nama, jenis FROM kategori ORDER BY nama");
$pelanggan_list = mysqli_query($koneksi, "SELECT id, nama FROM pelanggan ORDER BY nama");
$kendaraan_list = mysqli_query($koneksi, "SELECT k.id, k.no_plat, k.merek, k.pelanggan_id, p.nama AS pelanggan_nama FROM kendaraan k LEFT JOIN pelanggan p ON k.pelanggan_id = p.id ORDER BY p.nama, k.no_plat");
$sparepart_list = mysqli_query($koneksi, "SELECT id, nama, satuan, harga_jual FROM sparepart ORDER BY nama");

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $kategori_id = intval($_POST['kategori_id']);
    $pelanggan_id = intval($_POST['pelanggan_id']);
    $kendaraan_id = intval($_POST['kendaraan_id']);
    $sparepart_id = intval($_POST['sparepart_id']);
    $qty = intval($_POST['qty']);
    $harga_satuan = mysqli_real_escape_string($koneksi, $_POST['harga_satuan']);
    $subtotal = mysqli_real_escape_string($koneksi, $_POST['subtotal']);
    $jasa_detail = mysqli_real_escape_string($koneksi, $_POST['jasa_detail']);
    $nominal = mysqli_real_escape_string($koneksi, $_POST['nominal']);
    $user_id = $_SESSION['user_id'];

    $selected_tanggal = $tanggal;
    $selected_jenis = $jenis;
    $selected_kategori = $kategori_id;
    $selected_pelanggan = $pelanggan_id;
    $selected_kendaraan = $kendaraan_id;
    $selected_sparepart = $sparepart_id;
    $selected_qty = $qty;
    $selected_harga_satuan = $harga_satuan;
    $selected_subtotal = $subtotal;
    $selected_jasa_detail = $jasa_detail;
    $selected_nominal = $nominal;
    
    // Validasi
    if (empty($tanggal) || empty($jenis) || empty($kategori_id) || empty($pelanggan_id) || empty($kendaraan_id) || empty($nominal)) {
        $error = "Field yang wajib diisi tidak boleh kosong!";
    } elseif (!empty($sparepart_id) && $qty <= 0) {
        $error = "Jumlah sparepart harus lebih besar dari 0!";
    } else {
        // Cek apakah kolom baru sudah ada, jika belum tambahkan
        $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'unit_keterangan'");
        if (mysqli_num_rows($check_columns) == 0) {
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) AFTER kategori");
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT AFTER unit_keterangan");
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT AFTER jasa_detail");
        }
        $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'kategori_id'");
        if (mysqli_num_rows($check_columns) == 0) {
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN kategori_id INT NULL AFTER kategori");
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN pelanggan_id INT NULL AFTER user_id");
            mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN kendaraan_id INT NULL AFTER pelanggan_id");
        }

        $kategori_name = '';
        $result_kategori = mysqli_query($koneksi, "SELECT nama FROM kategori WHERE id = $kategori_id LIMIT 1");
        if ($result_kategori && mysqli_num_rows($result_kategori) > 0) {
            $kategori_name = mysqli_fetch_assoc($result_kategori)['nama'];
        }

        $pelanggan_name = '';
        $result_pelanggan = mysqli_query($koneksi, "SELECT nama FROM pelanggan WHERE id = $pelanggan_id LIMIT 1");
        if ($result_pelanggan && mysqli_num_rows($result_pelanggan) > 0) {
            $pelanggan_name = mysqli_fetch_assoc($result_pelanggan)['nama'];
        }

        $kendaraan_name = '';
        $result_kendaraan = mysqli_query($koneksi, "SELECT no_plat, merek FROM kendaraan WHERE id = $kendaraan_id LIMIT 1");
        if ($result_kendaraan && mysqli_num_rows($result_kendaraan) > 0) {
            $kendaraan_row = mysqli_fetch_assoc($result_kendaraan);
            $kendaraan_name = $kendaraan_row['no_plat'] . ' / ' . $kendaraan_row['merek'];
        }

        $sparepart_name = '';
        if (!empty($sparepart_id)) {
            $result_sparepart = mysqli_query($koneksi, "SELECT nama FROM sparepart WHERE id = $sparepart_id LIMIT 1");
            if ($result_sparepart && mysqli_num_rows($result_sparepart) > 0) {
                $sparepart_name = mysqli_fetch_assoc($result_sparepart)['nama'];
            }
        }

        $unit_keterangan = $kendaraan_name;
        $keterangan_full = "Pelanggan: " . $pelanggan_name . " | Kendaraan: " . $kendaraan_name;
        if (!empty($sparepart_name) && $qty > 0) {
            $keterangan_full .= " | Sparepart: " . $sparepart_name . " x " . $qty;
        }
        if (!empty($jasa_detail)) {
            $keterangan_full .= " | Jasa: " . $jasa_detail;
        }

        $barang_sparepart = '';
        if (!empty($sparepart_name) && $qty > 0) {
            $barang_sparepart = $sparepart_name . " x " . $qty;
        }

        $query = "INSERT INTO transaksi (tanggal, jenis, kategori, keterangan, unit_keterangan, jasa_detail, barang_sparepart, jumlah, user_id, kategori_id, pelanggan_id, kendaraan_id) 
                  VALUES ('$tanggal', '$jenis', '$kategori_name', '$keterangan_full', '$unit_keterangan', '$jasa_detail', '$barang_sparepart', '$nominal', '$user_id', '$kategori_id', '$pelanggan_id', '$kendaraan_id')";

        if (mysqli_query($koneksi, $query)) {
            $transaksi_id = mysqli_insert_id($koneksi);
            if (!empty($sparepart_id) && $qty > 0) {
                $check_detail_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'detail_transaksi'");
                if (mysqli_num_rows($check_detail_table) > 0) {
                    $insert_detail = "INSERT INTO detail_transaksi (transaksi_id, sparepart_id, nama_item, qty, harga_satuan, subtotal) 
                                      VALUES ($transaksi_id, $sparepart_id, '$sparepart_name', '$qty', '$harga_satuan', '$subtotal')";
                    mysqli_query($koneksi, $insert_detail);
                }
            }

            $success = "Data transaksi berhasil ditambahkan!";
            // Reset form setelah 2 detik
            header("refresh:2;url=create.php");
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
    <title>Tambah Transaksi - Bengkel Biyai Racing Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #dc3545;
        --secondary-color: #ffc107;
        --dark-color: #1a1a1a;
        --light-bg: #f8f9fa;
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        color: var(--secondary-color) !important;
        font-size: 1.5rem;
    }

    .navbar-brand i {
        color: var(--primary-color);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-control:disabled {
        background-color: #f8f9fa;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .btn-submit {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(235, 51, 73, 0.4);
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

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #667eea;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .info-box i {
        color: #667eea;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
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
                    <li class="nav-item">
                        <a class="nav-link active" href="create.php">
                            <i class="bi bi-plus-circle"></i> Tambah Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lihat.php">
                            <i class="bi bi-list-ul"></i> Lihat Semua
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../laporan/laporan_harian.php"><i
                                        class="bi bi-calendar-day"></i> Laporan Harian</a></li>
                            <li><a class="dropdown-item" href="../laporan/laporan_mingguan.php"><i
                                        class="bi bi-calendar-week"></i> Laporan Mingguan</a></li>
                            <li><a class="dropdown-item" href="../laporan/laporan_bulanan.php"><i
                                        class="bi bi-calendar-month"></i> Laporan Bulanan</a></li>
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
                <h3><i class="bi bi-plus-circle"></i> Tambah Transaksi Keuangan</h3>
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

            <div class="info-box">
                <i class="bi bi-info-circle"></i> <strong>Informasi:</strong> Field yang ditandai dengan <span
                    class="required">*</span> wajib diisi.
            </div>

            <form method="POST" action="" id="transaksiForm">
                <div class="row">
                    <!-- Tanggal Transaksi -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">
                            Tanggal Transaksi <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <input type="date" class="form-control with-icon" id="tanggal" name="tanggal"
                                value="<?php echo htmlspecialchars($selected_tanggal); ?>" required>
                        </div>
                    </div>

                    <!-- Jenis Transaksi -->
                    <div class="col-md-6 mb-3">
                        <label for="jenis" class="form-label">
                            Jenis Transaksi <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-arrow-left-right"></i>
                            </span>
                            <select class="form-select with-icon" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pemasukan" <?php echo $selected_jenis == 'pemasukan' ? 'selected' : ''; ?>>Pemasukan</option>
                                <option value="pengeluaran" <?php echo $selected_jenis == 'pengeluaran' ? 'selected' : ''; ?>>Pengeluaran</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Kategori Transaksi -->
                    <div class="col-md-6 mb-3">
                        <label for="kategori_id" class="form-label">
                            Kategori Transaksi <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-tags"></i>
                            </span>
                            <select class="form-select with-icon" id="kategori_id" name="kategori_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php if ($kategori_list): ?>
                                <?php while ($row = mysqli_fetch_assoc($kategori_list)): ?>
                                <option value="<?php echo $row['id']; ?>" data-jenis="<?php echo $row['jenis']; ?>" <?php echo $selected_kategori == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['nama']); ?> (<?php echo ucfirst($row['jenis']); ?>)
                                </option>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small class="text-muted">Pilih kategori transaksi yang sudah didefinisikan.</small>
                    </div>

                    <!-- Pelanggan -->
                    <div class="col-md-6 mb-3">
                        <label for="pelanggan_id" class="form-label">
                            Pelanggan <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-people"></i>
                            </span>
                            <select class="form-select with-icon" id="pelanggan_id" name="pelanggan_id" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php if ($pelanggan_list): ?>
                                <?php while ($row = mysqli_fetch_assoc($pelanggan_list)): ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $selected_pelanggan == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                </option>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small class="text-muted">Pilih pelanggan yang menerima jasa atau membeli produk.</small>
                    </div>
                </div>

                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-md-6 mb-3">
                        <label for="kendaraan_id" class="form-label">
                            Kendaraan <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-car-front"></i>
                            </span>
                            <select class="form-select with-icon" id="kendaraan_id" name="kendaraan_id" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                <?php if ($kendaraan_list): ?>
                                <?php while ($row = mysqli_fetch_assoc($kendaraan_list)): ?>
                                <option value="<?php echo $row['id']; ?>" data-pelanggan-id="<?php echo $row['pelanggan_id']; ?>" <?php echo $selected_kendaraan == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['no_plat'] . ' / ' . $row['merek'] . ' (' . $row['pelanggan_nama'] . ')'); ?>
                                </option>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small class="text-muted">Pilih kendaraan yang terkait dengan transaksi.</small>
                    </div>

                    <!-- Sparepart -->
                    <div class="col-md-6 mb-3">
                        <label for="sparepart_id" class="form-label">
                            Sparepart</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-box-seam"></i>
                            </span>
                            <select class="form-select with-icon" id="sparepart_id" name="sparepart_id">
                                <option value="">-- Pilih Sparepart --</option>
                                <?php if ($sparepart_list): ?>
                                <?php while ($row = mysqli_fetch_assoc($sparepart_list)): ?>
                                <option value="<?php echo $row['id']; ?>" data-price="<?php echo $row['harga_jual']; ?>" <?php echo $selected_sparepart == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['nama'] . ' (' . $row['satuan'] . ') - Rp ' . number_format($row['harga_jual'], 0, ',', '.')); ?>
                                </option>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small class="text-muted">Pilih sparepart yang digunakan (jika ada).</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="qty" class="form-label">Jumlah Sparepart</label>
                        <input type="number" min="0" step="1" class="form-control" id="qty" name="qty" value="<?php echo htmlspecialchars($selected_qty); ?>" placeholder="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="harga_satuan" class="form-label">Harga Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" min="0" step="0.01" class="form-control" id="harga_satuan" name="harga_satuan" value="<?php echo htmlspecialchars($selected_harga_satuan); ?>" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="subtotal" class="form-label">Subtotal Sparepart</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" min="0" step="0.01" class="form-control" id="subtotal" name="subtotal" value="<?php echo htmlspecialchars($selected_subtotal); ?>" placeholder="0.00" readonly>
                        </div>
                    </div>
                </div>

                <!-- Jasa dan Detail Pengerjaan -->
                <div class="mb-3">
                    <label for="jasa_detail" class="form-label">
                        <i class="bi bi-tools"></i> Jasa dan Detail Pengerjaan
                    </label>
                    <textarea class="form-control" id="jasa_detail" name="jasa_detail" rows="3" placeholder="Contoh: Service berkala, Ganti oli, Tune up mesin, dll"><?php echo htmlspecialchars($selected_jasa_detail); ?></textarea>
                    <small class="text-muted">Detail jasa yang dikerjakan atau diberikan.</small>
                </div>

                <!-- Nominal (Total) -->
                <div class="mb-4">
                    <label for="nominal" class="form-label">
                        Nominal (Total) <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-currency-exchange"></i> Rp
                        </span>
                        <input type="number" class="form-control" id="nominal" name="nominal" placeholder="0" min="0"
                            step="0.01" value="<?php echo htmlspecialchars($selected_nominal); ?>" required>
                    </div>
                    <small class="text-muted">Masukkan total nominal transaksi</small>
                </div>

                <!-- Button Actions -->
                <div class="d-flex gap-3 justify-content-end">
                    <a href="../index.php" class="btn btn-cancel">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const nominalInput = document.getElementById('nominal');
    const jenisSelect = document.getElementById('jenis');
    const kategoriSelect = document.getElementById('kategori_id');
    const pelangganSelect = document.getElementById('pelanggan_id');
    const kendaraanSelect = document.getElementById('kendaraan_id');
    const sparepartSelect = document.getElementById('sparepart_id');
    const qtyInput = document.getElementById('qty');
    const hargaSatuanInput = document.getElementById('harga_satuan');
    const subtotalInput = document.getElementById('subtotal');

    function filterKategori() {
        const selectedJenis = jenisSelect.value;
        Array.from(kategoriSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = selectedJenis && option.dataset.jenis !== selectedJenis;
        });
        if (kategoriSelect.value && kategoriSelect.selectedOptions[0].hidden) {
            kategoriSelect.value = '';
        }
    }

    function filterKendaraan() {
        const selectedPelanggan = pelangganSelect.value;
        Array.from(kendaraanSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = option.dataset.pelangganId !== selectedPelanggan;
        });
        if (kendaraanSelect.value && kendaraanSelect.selectedOptions[0].hidden) {
            kendaraanSelect.value = '';
        }
    }

    function updateSubtotal() {
        const qty = parseFloat(qtyInput.value) || 0;
        const harga = parseFloat(hargaSatuanInput.value) || 0;
        subtotalInput.value = (qty * harga).toFixed(2);
    }

    function updateHargaSatuan() {
        const selectedOption = sparepartSelect.selectedOptions[0];
        if (selectedOption && selectedOption.dataset.price) {
            hargaSatuanInput.value = selectedOption.dataset.price;
        } else {
            hargaSatuanInput.value = '';
        }
        updateSubtotal();
    }

    nominalInput.addEventListener('input', function(e) {
        let value = e.target.value;
        value = value.replace(/[^\d.]/g, '');
        e.target.value = value;
    });

    jenisSelect.addEventListener('change', filterKategori);
    pelangganSelect.addEventListener('change', filterKendaraan);
    sparepartSelect.addEventListener('change', updateHargaSatuan);
    qtyInput.addEventListener('input', updateSubtotal);
    hargaSatuanInput.addEventListener('input', updateSubtotal);

    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        const nominal = parseFloat(nominalInput.value) || 0;
        if (nominal <= 0) {
            e.preventDefault();
            alert('Nominal harus lebih besar dari 0!');
            return false;
        }
    });

    filterKategori();
    filterKendaraan();
    updateSubtotal();
    </script>
</body>

</html>