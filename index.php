<?php
session_start();
require_once 'DB/koneksi.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Query untuk mendapatkan statistik keuangan
$query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan'";
$query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran'";
$query_transaksi = "SELECT * FROM transaksi ORDER BY tanggal DESC LIMIT 10";

$result_pemasukan = mysqli_query($koneksi, $query_pemasukan);
$result_pengeluaran = mysqli_query($koneksi, $query_pengeluaran);
$result_transaksi = mysqli_query($koneksi, $query_transaksi);

$total_pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;
$total_pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan - Bengkel Biyai Racing Shop</title>
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

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
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

    .stat-icon {
        font-size: 3rem;
        opacity: 0.8;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 10px 0;
    }

    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-card {
        background: white;
    }

    .table {
        margin-bottom: 0;
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

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
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

    .btn-action {
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        color: white;
    }

    .welcome-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .welcome-title {
        color: var(--dark-color);
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .welcome-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .quick-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .footer {
        background: rgba(26, 26, 26, 0.95);
        color: white;
        text-align: center;
        padding: 20px;
        margin-top: 50px;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-speedometer2"></i> Bengkel Biyai Racing Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="CRUD/create.php">
                            <i class="bi bi-plus-circle"></i> Tambah Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="CRUD/lihat.php">
                            <i class="bi bi-list-ul"></i> Lihat Semua
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="laporan/laporan_harian.php"><i
                                        class="bi bi-calendar-day"></i> Laporan Harian</a></li>
                            <li><a class="dropdown-item" href="laporan/laporan_mingguan.php"><i
                                        class="bi bi-calendar-week"></i> Laporan Mingguan</a></li>
                            <li><a class="dropdown-item" href="laporan/laporan_bulanan.php"><i
                                        class="bi bi-calendar-month"></i> Laporan Bulanan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="bi bi-info-circle"></i> Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">
                <i class="bi bi-speedometer2 text-danger"></i> Dashboard Keuangan
            </h1>
            <p class="welcome-subtitle">Selamat datang di sistem manajemen keuangan Bengkel Biyai Racing Shop</p>
            <div class="quick-actions">
                <a href="CRUD/create.php" class="btn btn-success-custom btn-action">
                    <i class="bi bi-plus-circle"></i> Tambah Pemasukan
                </a>
                <a href="CRUD/create.php" class="btn btn-danger-custom btn-action">
                    <i class="bi bi-dash-circle"></i> Tambah Pengeluaran
                </a>
                <a href="CRUD/lihat.php" class="btn btn-primary-custom btn-action">
                    <i class="bi bi-list-ul"></i> Lihat Semua Transaksi
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card pemasukan">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-down-circle stat-icon"></i>
                        <div class="stat-label">Total Pemasukan</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card pengeluaran">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-up-circle stat-icon"></i>
                        <div class="stat-label">Total Pengeluaran</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card saldo">
                    <div class="card-body text-center">
                        <i class="bi bi-wallet2 stat-icon"></i>
                        <div class="stat-label">Saldo Saat Ini</div>
                        <div class="stat-value">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card table-card">
            <div class="card-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Transaksi Terbaru
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result_transaksi) > 0) {
                                while ($row = mysqli_fetch_assoc($result_transaksi)) {
                                    $badge_class = $row['jenis'] == 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran';
                                    $icon = $row['jenis'] == 'pemasukan' ? 'arrow-down-circle' : 'arrow-up-circle';
                                    ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                <td>
                                    <span class="<?php echo $badge_class; ?>">
                                        <i class="bi bi-<?php echo $icon; ?>"></i>
                                        <?php echo ucfirst($row['jenis']); ?>
                                    </span>
                                </td>
                                <td><strong>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></strong></td>
                                <td>
                                    <a href="CRUD/update.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="CRUD/delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="mt-3 text-muted">Belum ada transaksi. Mulai dengan menambahkan transaksi
                                        pertama Anda!</p>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Bengkel Biyai Racing Shop - Sistem Manajemen Keuangan</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>