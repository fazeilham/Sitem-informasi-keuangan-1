<?php
session_start();
require_once '../DB/koneksi.php';
require_once '../helpers.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil pesan dari URL (untuk notifikasi setelah hapus/edit)
$success_msg = isset($_GET['success']) ? $_GET['success'] : '';
$error_msg = isset($_GET['error']) ? $_GET['error'] : '';

// Query untuk mendapatkan semua transaksi
$query = "SELECT * FROM transaksi ORDER BY tanggal DESC, created_at DESC";
$result = mysqli_query($koneksi, $query);

// Query untuk statistik
$query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan'";
$query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran'";
$result_pemasukan = mysqli_query($koneksi, $query_pemasukan);
$result_pengeluaran = mysqli_query($koneksi, $query_pengeluaran);

$total_pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;
$total_pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
$total_transaksi = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Semua Transaksi - Bengkel Biyai Racing Shop</title>
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

    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        white-space: nowrap;
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
        font-weight: 600;
    }

    .badge-pengeluaran {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
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

    .detail-text {
        font-size: 0.9rem;
        color: #6c757d;
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .detail-text-full {
        font-size: 0.85rem;
        color: #495057;
        line-height: 1.5;
    }

    .header-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .header-title {
        color: var(--dark-color);
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .footer {
        background: rgba(26, 26, 26, 0.95);
        color: white;
        text-align: center;
        padding: 20px;
        margin-top: 50px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 5rem;
        color: #ccc;
        margin-bottom: 20px;
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
                            <li><a class="dropdown-item" href="create_pemasukan.php">Tambah Pemasukan</a></li>
                            <li><a class="dropdown-item" href="create_pengeluaran.php">Tambah Pengeluaran</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="lihat.php">
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
        <!-- Alert Messages -->
        <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error_msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="header-section">
            <h1 class="header-title">
                <i class="bi bi-list-ul text-danger"></i> Semua Transaksi Keuangan
            </h1>
            <p class="text-muted mb-3">Daftar lengkap semua transaksi keuangan Bengkel Biyai Racing Shop</p>
            <div class="export-buttons">
                <div class="btn-group">
                    <a href="create_pemasukan.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Pemasukan
                    </a>
                    <a href="create_pengeluaran.php" class="btn btn-danger">
                        <i class="bi bi-dash-circle"></i> Tambah Pengeluaran
                    </a>
                </div>
                <a href="../index.php" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
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
                        <div class="stat-label">Total Pemasukan</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card pengeluaran">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-up-circle stat-icon"></i>
                        <div class="stat-label">Total Pengeluaran</div>
                        <div class="stat-value">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card saldo">
                    <div class="card-body text-center">
                        <i class="bi bi-wallet2 stat-icon"></i>
                        <div class="stat-label">Saldo Saat Ini</div>
                        <div class="stat-value">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card table-card">
            <div class="card-header"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($total_transaksi > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $badge_class = $row['jenis'] == 'pemasukan' ? 'badge-pemasukan' : 'badge-pengeluaran';
                                    $icon = $row['jenis'] == 'pemasukan' ? 'arrow-down-circle' : 'arrow-up-circle';
                                    
                                    // Ambil data dari kolom baru jika ada, jika tidak gunakan keterangan lama
                                    $unit_keterangan = !empty($row['unit_keterangan']) ? $row['unit_keterangan'] : '-';
                                    $jasa_detail = !empty($row['jasa_detail']) ? $row['jasa_detail'] : '-';
                                    $barang_sparepart = !empty($row['barang_sparepart']) ? $row['barang_sparepart'] : '-';

                                    $detail_items = '';
                                    $detail_q = mysqli_query($koneksi, "SELECT sparepart_id, nama_item, qty, harga_satuan, subtotal FROM detail_transaksi WHERE transaksi_id = '" . intval($row['id']) . "'");
                                    if ($detail_q && mysqli_num_rows($detail_q) > 0) {
                                        $items = [];
                                        while ($d = mysqli_fetch_assoc($detail_q)) {
                                            $item_label = htmlspecialchars($d['nama_item']);
                                            $qty_text = intval($d['qty']) > 0 ? intval($d['qty']) . ' x ' : '';
                                            $harga_text = 'Rp ' . number_format($d['harga_satuan'], 0, ',', '.');
                                            $subtotal_text = 'Rp ' . number_format($d['subtotal'], 0, ',', '.');
                                            $items[] = htmlspecialchars($qty_text) . $item_label . ' (' . $harga_text . ', Subtotal ' . $subtotal_text . ')';
                                        }
                                        $detail_items = implode('<br>', $items);
                                    }
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
                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($row['kategori']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <div class="detail-text-full">
                                        <?php echo htmlspecialchars($unit_keterangan); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="detail-text-full">
                                        <?php echo htmlspecialchars($jasa_detail); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="detail-text-full">
                                        <?php echo htmlspecialchars($barang_sparepart); ?>
                                        <?php if (!empty($detail_items)): ?><br><small><?php echo $detail_items; ?></small><?php endif; ?>
                                    </div>
                                </td>
                                <td
                                    class="fw-bold <?php echo $row['jenis'] == 'pemasukan' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $row['jenis'] == 'pemasukan' ? '+' : '-'; ?>Rp
                                    <?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus transaksi ini?')"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h4 class="mt-3 text-muted">Belum Ada Transaksi</h4>
                                    <p class="text-muted">Mulai dengan menambahkan transaksi pertama Anda!</p>
                                    <div class="btn-group">
                                        <a href="create_pemasukan.php" class="btn btn-success btn-lg mt-3">
                                            <i class="bi bi-plus-circle"></i> Tambah Pemasukan Pertama
                                        </a>
                                        <a href="create_pengeluaran.php" class="btn btn-danger btn-lg mt-3">
                                            <i class="bi bi-dash-circle"></i> Tambah Pengeluaran Pertama
                                        </a>
                                    </div>
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