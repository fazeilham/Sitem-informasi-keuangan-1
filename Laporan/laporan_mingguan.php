<?php
session_start();
require_once '../DB/koneksi.php';
require_once '../helpers.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil tanggal awal minggu dari parameter atau gunakan minggu ini
if (isset($_GET['tanggal_awal'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));
} else {
    // Default: minggu ini (Senin - Minggu)
    $day = date('w');
    $tanggal_awal = date('Y-m-d', strtotime('-' . ($day == 0 ? 6 : $day - 1) . ' days'));
    $tanggal_akhir = date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));
}

// Query untuk mendapatkan transaksi mingguan
$query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC, created_at DESC";
$result = mysqli_query($koneksi, $query);

// Query untuk statistik mingguan
$query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
$query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
$result_pemasukan = mysqli_query($koneksi, $query_pemasukan);
$result_pengeluaran = mysqli_query($koneksi, $query_pengeluaran);

$total_pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;
$total_pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
$total_transaksi = mysqli_num_rows($result);

// Format tanggal untuk display
$tanggal_awal_display = date('d F Y', strtotime($tanggal_awal));
$tanggal_akhir_display = date('d F Y', strtotime($tanggal_akhir));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mingguan - Bengkel Biyai Racing Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
    :root {
        --primary-color: #dc3545;
        --secondary-color: #ffc107;
        --dark-color: #1a1a1a;
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

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }

    .header-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
    }

    .stat-card.pemasukan {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .stat-card.pengeluaran {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    }

    .stat-card.saldo {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
    }

    .table-card {
        background: white;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table thead th {
        border: none;
        padding: 15px;
        font-weight: 600;
    }

    .badge-pemasukan {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .badge-pengeluaran {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .btn-print {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        color: white;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-plus-circle"></i> Tambah Transaksi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../CRUD/create_pemasukan.php">Tambah Pemasukan</a></li>
                            <li><a class="dropdown-item" href="../CRUD/create_pengeluaran.php">Tambah Pengeluaran</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../CRUD/lihat.php">
                            <i class="bi bi-list-ul"></i> Lihat Semua
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="laporanDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="laporan_harian.php"><i class="bi bi-calendar-day"></i>
                                    Laporan Harian</a></li>
                            <li><a class="dropdown-item active" href="laporan_mingguan.php"><i
                                        class="bi bi-calendar-week"></i> Laporan Mingguan</a></li>
                            <li><a class="dropdown-item" href="laporan_bulanan.php"><i class="bi bi-calendar-month"></i>
                                    Laporan Bulanan</a></li>
                        </ul>
                    </li>
                    <?php if (is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../about.php">
                            <i class="bi bi-info-circle"></i> Tentang
                        </a>
                    </li>
                    <?php endif; ?>
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
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-2">
                        <i class="bi bi-calendar-week text-danger"></i> Laporan Mingguan
                    </h1>
                    <p class="text-muted mb-0">Laporan transaksi keuangan per minggu</p>
                </div>
                <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
                    <form method="GET" action="" class="d-flex gap-2">
                        <input type="date" name="tanggal_awal" class="form-control" value="<?php echo $tanggal_awal; ?>"
                            required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </form>
                    <a href="cetak_pdf.php?type=mingguan&tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>"
                        target="_blank" class="btn btn-print">
                        <i class="bi bi-file-pdf"></i> Cetak PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body text-center">
                <h4 class="mb-0">
                    <i class="bi bi-calendar-range"></i> Periode: <?php echo $tanggal_awal_display; ?> -
                    <?php echo $tanggal_akhir_display; ?>
                </h4>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card info">
                    <div class="card-body text-center">
                        <i class="bi bi-list-check stat-icon"></i>
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value"><?php echo number_format($total_transaksi, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card pemasukan">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-down-circle stat-icon"></i>
                        <div class="stat-label">Pemasukan</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card pengeluaran">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-up-circle stat-icon"></i>
                        <div class="stat-label">Pengeluaran</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card saldo">
                    <div class="card-body text-center">
                        <i class="bi bi-wallet2 stat-icon"></i>
                        <div class="stat-label">Saldo</div>
                        <div class="stat-value">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card table-card">
            <div class="card-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Daftar Transaksi (<?php echo $total_transaksi; ?> data)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Unit/Keterangan</th>
                                <th>Jasa & Detail</th>
                                <th>Sparepart</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($total_transaksi > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $badge_class = $row['jenis'] == 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran';
                                    $icon = $row['jenis'] == 'pemasukan' ? 'arrow-down-circle' : 'arrow-up-circle';
                                    
                                    $unit_keterangan = !empty($row['unit_keterangan']) ? $row['unit_keterangan'] : '-';
                                    $jasa_detail = !empty($row['jasa_detail']) ? $row['jasa_detail'] : '-';
                                    $barang_sparepart = !empty($row['barang_sparepart']) ? $row['barang_sparepart'] : '-';
                                    ?>
                            <tr>
                                <td><strong><?php echo $no++; ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td>
                                    <span class="<?php echo $badge_class; ?>">
                                        <i class="bi bi-<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($row['jenis']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['kategori']); ?></strong></td>
                                <td><?php echo htmlspecialchars($unit_keterangan); ?></td>
                                <td><?php echo htmlspecialchars($jasa_detail); ?></td>
                                <td><?php echo htmlspecialchars($barang_sparepart); ?></td>
                                <td>
                                    <strong
                                        style="color: <?php echo $row['jenis'] == 'pemasukan' ? '#28a745' : '#dc3545'; ?>;">
                                        <?php echo $row['jenis'] == 'pemasukan' ? '+' : '-'; ?>Rp
                                        <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                                    </strong>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="mt-3 text-muted">Tidak ada transaksi pada periode ini</p>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>