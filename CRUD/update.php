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
$data = null;

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data transaksi untuk diedit
if ($id > 0) {
    $query = "SELECT * FROM transaksi WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $error = "Transaksi tidak ditemukan!";
        header("Location: lihat.php?error=" . urlencode($error));
        exit();
    }
} else {
    $error = "ID transaksi tidak valid!";
    header("Location: lihat.php?error=" . urlencode($error));
    exit();
}

// Query master data
$kategori_list = array();
$result_kategori = mysqli_query($koneksi, "SELECT id, nama, jenis FROM kategori ORDER BY nama");
while ($row = mysqli_fetch_assoc($result_kategori)) {
    $kategori_list[] = $row;
}

$pelanggan_list = array();
$result_pelanggan = mysqli_query($koneksi, "SELECT id, nama FROM pelanggan ORDER BY nama");
while ($row = mysqli_fetch_assoc($result_pelanggan)) {
    $pelanggan_list[] = $row;
}

$kendaraan_list = array();
$result_kendaraan = mysqli_query($koneksi, "SELECT k.id, k.no_plat, k.merek, k.pelanggan_id, p.nama AS pelanggan_nama FROM kendaraan k LEFT JOIN pelanggan p ON k.pelanggan_id = p.id ORDER BY p.nama, k.no_plat");
while ($row = mysqli_fetch_assoc($result_kendaraan)) {
    $kendaraan_list[] = $row;
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $kategori_id = intval($_POST['kategori_id']);
    $pelanggan_id = isset($_POST['pelanggan_id']) ? intval($_POST['pelanggan_id']) : 0;
    $kendaraan_id = isset($_POST['kendaraan_id']) ? intval($_POST['kendaraan_id']) : 0;
    $jasa_detail = mysqli_real_escape_string($koneksi, $_POST['jasa_detail']);
    $barang_sparepart = mysqli_real_escape_string($koneksi, $_POST['barang_sparepart']);
    $nominal = mysqli_real_escape_string($koneksi, $_POST['nominal']);
    
    // Validasi
    if (empty($tanggal) || empty($jenis) || empty($kategori_id) || empty($nominal)) {
        $error = "Field yang wajib diisi tidak boleh kosong!";
    } else {
        $kategori_name = '';
        $result_kat = mysqli_query($koneksi, "SELECT nama FROM kategori WHERE id = $kategori_id");
        if ($row_kat = mysqli_fetch_assoc($result_kat)) {
            $kategori_name = $row_kat['nama'];
        }

        // Update query
        $update_query = "UPDATE transaksi SET 
                        tanggal = '$tanggal',
                        jenis = '$jenis',
                        kategori = '$kategori_name',
                        kategori_id = $kategori_id,
                        pelanggan_id = " . ($pelanggan_id > 0 ? $pelanggan_id : "NULL") . ",
                        kendaraan_id = " . ($kendaraan_id > 0 ? $kendaraan_id : "NULL") . ",
                        jasa_detail = '$jasa_detail',
                        barang_sparepart = '$barang_sparepart',
                        jumlah = '$nominal'
                        WHERE id = $id";
        
        if (mysqli_query($koneksi, $update_query)) {
            $success = "Data transaksi berhasil diupdate!";
            header("refresh:2;url=lihat.php?success=" . urlencode($success));
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }
    }
}

// Set nilai default jika data belum diisi
$tanggal = $data['tanggal'] ?? date('Y-m-d');
$jenis = $data['jenis'] ?? '';
$kategori = $data['kategori'] ?? '';
$unit_keterangan = $data['unit_keterangan'] ?? '';
$jasa_detail = $data['jasa_detail'] ?? '';
$barang_sparepart = $data['barang_sparepart'] ?? '';
$nominal = $data['jumlah'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Bengkel Biyai Racing Shop</title>
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
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .btn-submit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(240, 147, 251, 0.4);
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
                        <a class="nav-link" href="create.php">
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
                <h3><i class="bi bi-pencil-square"></i> Edit Transaksi Keuangan</h3>
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
                                value="<?php echo htmlspecialchars($tanggal); ?>" required>
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
                                <option value="pemasukan" <?php echo $jenis == 'pemasukan' ? 'selected' : ''; ?>>
                                    Pemasukan</option>
                                <option value="pengeluaran" <?php echo $jenis == 'pengeluaran' ? 'selected' : ''; ?>>
                                    Pengeluaran</option>
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
                                <?php foreach ($kategori_list as $row): ?>
                                <option value="<?php echo $row['id']; ?>" data-jenis="<?php echo $row['jenis']; ?>"
                                    <?php echo ($data['kategori_id'] ?? 0) == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                    (<?php echo ucfirst($row['jenis']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Pelanggan -->
                    <div class="col-md-6 mb-3">
                        <label for="pelanggan_id" class="form-label">
                            Pelanggan <span class="required" id="req_pelanggan">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-people"></i>
                            </span>
                            <select class="form-select with-icon" id="pelanggan_id" name="pelanggan_id">
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($pelanggan_list as $row): ?>
                                <option value="<?php echo $row['id']; ?>"
                                    <?php echo ($data['pelanggan_id'] ?? 0) == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-md-6 mb-3">
                        <label for="kendaraan_id" class="form-label">
                            Kendaraan <span class="required" id="req_kendaraan">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-car-front"></i>
                            </span>
                            <select class="form-select with-icon" id="kendaraan_id" name="kendaraan_id">
                                <option value="">-- Pilih Kendaraan --</option>
                                <?php foreach ($kendaraan_list as $row): ?>
                                <option value="<?php echo $row['id']; ?>"
                                    data-pelanggan-id="<?php echo $row['pelanggan_id']; ?>"
                                    <?php echo ($data['kendaraan_id'] ?? 0) == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['no_plat'] . ' / ' . $row['merek'] . ' (' . $row['pelanggan_nama'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Unit Keterangan (Legacy) -->
                    <div class="col-md-6 mb-3">
                        <label for="unit_keterangan" class="form-label">
                            Unit / Keterangan (Legacy)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-info-circle"></i>
                            </span>
                            <input type="text" class="form-control with-icon" id="unit_keterangan" name="unit_keterangan"
                                value="<?php echo htmlspecialchars($unit_keterangan); ?>" placeholder="Unit atau kendaraan">
                        </div>
                    </div>
                </div>

                <!-- Jasa dan Detail Pengerjaan -->
                <div class="mb-3">
                    <label for="jasa_detail" class="form-label">
                        <i class="bi bi-tools"></i> Jasa dan Detail Pengerjaan
                    </label>
                    <textarea class="form-control" id="jasa_detail" name="jasa_detail" rows="3"
                        placeholder="Contoh: Service berkala, Ganti oli, Tune up mesin, dll"><?php echo htmlspecialchars($jasa_detail); ?></textarea>
                    <small class="text-muted">Detail jasa yang dikerjakan atau diberikan</small>
                </div>

                <!-- Barang dan Harga Sparepart -->
                <div class="mb-3">
                    <label for="barang_sparepart" class="form-label">
                        <i class="bi bi-box-seam"></i> Barang dan Harga Sparepart
                    </label>
                    <textarea class="form-control" id="barang_sparepart" name="barang_sparepart" rows="3"
                        placeholder="Contoh: Oli mesin 1L x Rp 50.000, Filter udara x Rp 25.000, dll"><?php echo htmlspecialchars($barang_sparepart); ?></textarea>
                    <small class="text-muted">Daftar sparepart yang digunakan beserta harganya</small>
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
                        <input type="number" class="form-control" id="nominal" name="nominal"
                            value="<?php echo htmlspecialchars($nominal); ?>" placeholder="0" min="0" step="0.01"
                            required>
                    </div>
                    <small class="text-muted">Masukkan total nominal transaksi</small>
                </div>

                <!-- Button Actions -->
                <div class="d-flex gap-3 justify-content-end">
                    <a href="lihat.php" class="btn btn-cancel">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle"></i> Update Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Format nominal saat input
    document.getElementById('nominal').addEventListener('input', function(e) {
        let value = e.target.value;
        // Hapus karakter selain angka dan titik
        value = value.replace(/[^\d.]/g, '');
        e.target.value = value;
    });

    // Validasi form sebelum submit
    const jenisSelect = document.getElementById('jenis');
    const kategoriSelect = document.getElementById('kategori_id');
    const pelangganSelect = document.getElementById('pelanggan_id');
    const kendaraanSelect = document.getElementById('kendaraan_id');

    function filterKategori() {
        const selectedJenis = jenisSelect.value;
        Array.from(kategoriSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = selectedJenis && option.dataset.jenis !== selectedJenis;
        });

        // Toggle required for pelanggan and kendaraan
        const isPemasukan = selectedJenis === 'pemasukan';
        document.getElementById('req_pelanggan').style.display = isPemasukan ? 'inline' : 'none';
        document.getElementById('req_kendaraan').style.display = isPemasukan ? 'inline' : 'none';
        pelangganSelect.required = isPemasukan;
        kendaraanSelect.required = isPemasukan;
    }

    function filterKendaraan() {
        const selectedPelanggan = pelangganSelect.value;
        Array.from(kendaraanSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = selectedPelanggan && option.dataset.pelangganId !== selectedPelanggan;
        });
    }

    jenisSelect.addEventListener('change', filterKategori);
    pelangganSelect.addEventListener('change', filterKendaraan);

    // Run filters on load
    filterKategori();
    filterKendaraan();

    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        const nominal = document.getElementById('nominal').value;
        if (nominal <= 0) {
            e.preventDefault();
            alert('Nominal harus lebih besar dari 0!');
            return false;
        }
    });
    </script>
</body>

</html>