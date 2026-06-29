<?php
session_start();
require_once '../DB/koneksi.php';

// Ambil daftar kategori (hanya pemasukan)
$kategori_result = mysqli_query($koneksi, "SELECT * FROM kategori WHERE jenis = 'pemasukan' ORDER BY nama");
// Ambil daftar pelanggan untuk dropdown
$pelanggan_result = mysqli_query($koneksi, "SELECT id, nama FROM pelanggan ORDER BY nama ASC");
// Ambil daftar sparepart dari master sparepart
$sparepart_result = mysqli_query($koneksi, "SELECT id, nama FROM sparepart ORDER BY nama ASC");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';
$selected_tanggal = date('Y-m-d');
$selected_pelanggan_id = '';
$selected_kendaraan_id = '';
$selected_sparepart_id = '';
$selected_sparepart_lainnya = '';
$selected_kategori_id = '';
$selected_metode_pembayaran = '';
$selected_qty = '';
$selected_harga_satuan = '';
$selected_subtotal = '';
$selected_jasa_detail = '';
$selected_nominal = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $pelanggan_id = isset($_POST['pelanggan_id']) ? intval($_POST['pelanggan_id']) : 0;
    $kendaraan_id = isset($_POST['kendaraan_id']) ? intval($_POST['kendaraan_id']) : 0;
    $sparepart_id = $_POST['sparepart_id'] ?? '';
    $sparepart_lainnya = mysqli_real_escape_string($koneksi, $_POST['sparepart_lainnya'] ?? '');
    $kategori_id = isset($_POST['kategori_id']) ? intval($_POST['kategori_id']) : 0;
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    $harga_satuan = isset($_POST['harga_satuan']) ? mysqli_real_escape_string($koneksi, $_POST['harga_satuan']) : '0';
    $subtotal = isset($_POST['subtotal']) ? mysqli_real_escape_string($koneksi, $_POST['subtotal']) : '0';
    $jasa_detail = isset($_POST['jasa_detail']) ? mysqli_real_escape_string($koneksi, $_POST['jasa_detail']) : '';
    $nominal = isset($_POST['nominal']) ? mysqli_real_escape_string($koneksi, $_POST['nominal']) : '0';
    $user_id = $_SESSION['user_id'];

    $selected_tanggal = $tanggal;
    $selected_pelanggan_id = $pelanggan_id;
    $selected_kendaraan_id = $kendaraan_id;
    $selected_sparepart_id = $sparepart_id;
    $selected_sparepart_lainnya = $sparepart_lainnya;
    $selected_kategori_id = $kategori_id;
    $selected_metode_pembayaran = $metode_pembayaran;
    $selected_qty = $qty;
    $selected_harga_satuan = $harga_satuan;
    $selected_subtotal = $subtotal;
    $selected_jasa_detail = $jasa_detail;
    $selected_nominal = $nominal;

    if (empty($tanggal) || empty($pelanggan_id) || empty($kategori_id) || empty($nominal)) {
        $error = "Tanggal, Pelanggan, Kategori, dan Nominal wajib diisi!";
    } else {
        // Ambil nama kategori untuk keterangan
        $kategori_name = '';
        $kq = mysqli_query($koneksi, "SELECT nama FROM kategori WHERE id = '$kategori_id' LIMIT 1");
        if ($kq && $kr = mysqli_fetch_assoc($kq)) {
            $kategori_name = $kr['nama'];
        }
        // Ambil nama pelanggan untuk keterangan
        $pelanggan_name = '';
        $pq = mysqli_query($koneksi, "SELECT nama FROM pelanggan WHERE id = '$pelanggan_id' LIMIT 1");
        if ($pq && $pr = mysqli_fetch_assoc($pq)) {
            $pelanggan_name = $pr['nama'];
        }
        // Ambil data kendaraan untuk keterangan
        $kendaraan_label = '';
        if ($kendaraan_id > 0) {
            $kq2 = mysqli_query($koneksi, "SELECT no_plat, merek FROM kendaraan WHERE id = '$kendaraan_id' LIMIT 1");
            if ($kq2 && $kr2 = mysqli_fetch_assoc($kq2)) {
                $kendaraan_label = $kr2['no_plat'] . ' / ' . $kr2['merek'];
            }
        }

        $barang_sparepart = '';
        if ($sparepart_id === 'lainnya') {
            $barang_sparepart = $sparepart_lainnya;
        } elseif (ctype_digit(strval($sparepart_id)) && intval($sparepart_id) > 0) {
            $spq = mysqli_query($koneksi, "SELECT nama FROM sparepart WHERE id = '" . intval($sparepart_id) . "' LIMIT 1");
            if ($spq && $spr = mysqli_fetch_assoc($spq)) {
                $barang_sparepart = $spr['nama'];
            }
        }

        $keterangan_full = "Kategori: " . $kategori_name;
        if (!empty($pelanggan_name)) $keterangan_full .= " | Pelanggan: " . $pelanggan_name;
        if (!empty($kendaraan_label)) $keterangan_full .= " | Kendaraan: " . $kendaraan_label;
        if (!empty($barang_sparepart)) $keterangan_full .= " | Sparepart: " . $barang_sparepart;
        if (!empty($jasa_detail)) $keterangan_full .= " | Jasa: " . $jasa_detail;
        if (!empty($metode_pembayaran)) $keterangan_full .= " | Metode: " . $metode_pembayaran;

        $kendaraan_id_value = $kendaraan_id > 0 ? $kendaraan_id : 'NULL';
        $query = "INSERT INTO transaksi (tanggal, jenis, pelanggan_id, kategori_id, kendaraan_id, keterangan, unit_keterangan, jasa_detail, barang_sparepart, jumlah, user_id) 
              VALUES ('$tanggal', 'pemasukan', $pelanggan_id, $kategori_id, $kendaraan_id_value, '$keterangan_full', '$kendaraan_label', '$jasa_detail', '$barang_sparepart', '$nominal', '$user_id')";

        if (mysqli_query($koneksi, $query)) {
            $transaksi_id = mysqli_insert_id($koneksi);
            if (!empty($barang_sparepart) && $transaksi_id > 0 && $qty > 0) {
                $detail_sparepart_id = ($sparepart_id !== 'lainnya' && ctype_digit(strval($sparepart_id))) ? intval($sparepart_id) : 'NULL';
                $detail_nama_item = mysqli_real_escape_string($koneksi, $barang_sparepart);
                $detail_query = "INSERT INTO detail_transaksi (transaksi_id, sparepart_id, nama_item, qty, harga_satuan, subtotal) 
                    VALUES ($transaksi_id, " . ($detail_sparepart_id !== 'NULL' ? $detail_sparepart_id : 'NULL') . ", '$detail_nama_item', $qty, $harga_satuan, $subtotal)";
                if (!mysqli_query($koneksi, $detail_query)) {
                    $error = "Data pemasukan berhasil ditambahkan, tetapi detail transaksi gagal: " . mysqli_error($koneksi);
                }
            }
            $success = "Data pemasukan berhasil ditambahkan!";
            $selected_tanggal = date('Y-m-d');
            $selected_pelanggan_id = ''; $selected_kendaraan_id = ''; $selected_sparepart_id = ''; $selected_sparepart_lainnya = '';
            $selected_kategori_id = ''; $selected_metode_pembayaran = '';
            $selected_qty = ''; $selected_harga_satuan = ''; $selected_subtotal = '';
            $selected_jasa_detail = ''; $selected_nominal = '';
            header("refresh:2;url=create_pemasukan.php");
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
    <title>Tambah Pemasukan - Bengkel Biyai Racing Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #11998e;
        --secondary-color: #38ef7d;
    }

    body {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        border-color: #11998e;
        box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
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
                            <li><a class="dropdown-item active" href="create_pemasukan.php">Tambah Pemasukan</a></li>
                            <li><a class="dropdown-item" href="create_pengeluaran.php">Tambah Pengeluaran</a></li>
                        </ul>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database"></i> Master Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="kategori/index.php"><i class="bi bi-tags"></i>
                                    Kategori</a></li>
                            <li><a class="dropdown-item" href="pelanggan/index.php"><i class="bi bi-people"></i>
                                    Pelanggan</a></li>
                            <li><a class="dropdown-item" href="sparepart/index.php"><i class="bi bi-gear"></i>
                                    Sparepart</a></li>
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
                <h3><i class="bi bi-arrow-down-circle"></i> Tambah Pemasukan</h3>
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
                        <label for="tanggal" class="form-label">Tanggal Transaksi <span
                                class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" class="form-control with-icon" id="tanggal" name="tanggal"
                                value="<?php echo htmlspecialchars($selected_tanggal); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span
                                class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                            <select class="form-select with-icon" id="metode_pembayaran" name="metode_pembayaran"
                                required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Cash"
                                    <?php echo $selected_metode_pembayaran == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                <option value="Transfer"
                                    <?php echo $selected_metode_pembayaran == 'Transfer' ? 'selected' : ''; ?>>Transfer
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pelanggan_id" class="form-label">Pelanggan <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select with-icon" id="pelanggan_id" name="pelanggan_id" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php if ($pelanggan_result): while ($p = mysqli_fetch_assoc($pelanggan_result)): ?>
                                <option value="<?php echo $p['id']; ?>"
                                    <?php echo $selected_pelanggan_id == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['nama']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kendaraan_id" class="form-label">Kendaraan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                            <select class="form-select with-icon" id="kendaraan_id" name="kendaraan_id" disabled>
                                <option value="">-- Pilih Kendaraan --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori_id" class="form-label">Kategori Transaksi <span
                                class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tags"></i></span>
                            <select class="form-select with-icon" id="kategori_id" name="kategori_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php if ($kategori_result): while ($k = mysqli_fetch_assoc($kategori_result)): ?>
                                <option value="<?php echo $k['id']; ?>"
                                    <?php echo $selected_kategori_id == $k['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($k['nama']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sparepart_id" class="form-label">Sparepart</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                            <select class="form-select with-icon" id="sparepart_id" name="sparepart_id">
                                <option value="">-- Pilih Sparepart --</option>
                                <?php if ($sparepart_result): while ($s = mysqli_fetch_assoc($sparepart_result)): ?>
                                <option value="<?php echo $s['id']; ?>"
                                    <?php echo $selected_sparepart_id == $s['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['nama']); ?></option>
                                <?php endwhile; endif; ?>
                                <option value="lainnya"
                                    <?php echo $selected_sparepart_id === 'lainnya' ? 'selected' : ''; ?>>Lainnya
                                </option>
                            </select>
                        </div>
                        <div class="mt-3 sparepart-lainnya-container"
                            style="<?php echo $selected_sparepart_id === 'lainnya' ? '' : 'display:none;'; ?>">
                            <input type="text" class="form-control with-icon" id="sparepart_lainnya"
                                name="sparepart_lainnya"
                                value="<?php echo htmlspecialchars($selected_sparepart_lainnya); ?>"
                                placeholder="Masukkan sparepart lain">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="qty" class="form-label">Qty</label>
                        <input type="number" class="form-control" id="qty" name="qty" min="1"
                            value="<?php echo htmlspecialchars($selected_qty); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="harga_satuan" class="form-label">Harga Satuan</label>
                        <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" min="0"
                            step="0.01" value="<?php echo htmlspecialchars($selected_harga_satuan); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="subtotal" class="form-label">Subtotal</label>
                        <input type="number" class="form-control" id="subtotal" name="subtotal" min="0" step="0.01"
                            value="<?php echo htmlspecialchars($selected_subtotal); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="jasa_detail" class="form-label"><i class="bi bi-tools"></i> Jasa dan Detail
                        Pengerjaan</label>
                    <textarea class="form-control" id="jasa_detail" name="jasa_detail" rows="3"
                        placeholder="Contoh: Service berkala, Ganti oli, Tune up mesin"><?php echo htmlspecialchars($selected_jasa_detail); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="nominal" class="form-label">Nominal (Total) <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-currency-exchange"></i> Rp</span>
                        <input type="number" class="form-control" id="nominal" name="nominal" placeholder="0" min="0"
                            step="0.01" value="<?php echo htmlspecialchars($selected_nominal); ?>" required>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="../index.php" class="btn btn-cancel">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle"></i> Simpan Pemasukan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const pelangganSelect = document.querySelector('#pelanggan_id');
    const kendaraanSelect = document.querySelector('#kendaraan_id');
    const sparepartSelect = document.querySelector('#sparepart_id');
    const sparepartLainnyaContainer = document.querySelector('.sparepart-lainnya-container');

    function clearKendaraanOptions() {
        kendaraanSelect.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
        kendaraanSelect.disabled = true;
    }

    function loadKendaraan(pelangganId) {
        clearKendaraanOptions();
        if (!pelangganId) return;

        fetch('kendaraan/get_kendaraan.php?pelanggan_id=' + encodeURIComponent(pelangganId))
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.no_plat + ' - ' + item.merek + ' (' + item.tahun + ')';
                        if (item.id == <?php echo json_encode($selected_kendaraan_id); ?>) {
                            option.selected = true;
                        }
                        kendaraanSelect.appendChild(option);
                    });
                    if (data.data.length > 0) {
                        kendaraanSelect.disabled = false;
                    }
                }
            })
            .catch(() => {
                clearKendaraanOptions();
            });
    }

    function toggleSparepartLainnya() {
        if (!sparepartSelect) return;
        sparepartLainnyaContainer.style.display = sparepartSelect.value === 'lainnya' ? '' : 'none';
    }

    pelangganSelect.addEventListener('change', function() {
        loadKendaraan(this.value);
    });

    if (sparepartSelect) {
        sparepartSelect.addEventListener('change', toggleSparepartLainnya);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (pelangganSelect.value) {
            loadKendaraan(pelangganSelect.value);
        }
        toggleSparepartLainnya();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>