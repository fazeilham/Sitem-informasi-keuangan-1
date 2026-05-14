<?php
session_start();
require_once 'DB/koneksi.php';

// Redirect ke login jika belum autentikasi
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang - Bengkel Biyai Racing Shop</title>
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
        backdrop-filter: blur(8px);
    }

    .navbar-brand {
        color: var(--secondary-color) !important;
        font-weight: 700;
    }

    .card-transparent {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        font-size: 1.6rem;
        color: var(--primary-color);
    }

    .logo-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fff 0%, #f1f1f1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
    }

    footer {
        background: rgba(26, 26, 26, 0.95);
        color: white;
        text-align: center;
        padding: 18px;
        margin-top: 36px;
        border-radius: 8px;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><i class="bi bi-speedometer2 text-danger"></i> Bengkel Biyai Racing
                Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door"></i>
                            Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-info-circle"></i>
                            Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i>
                            Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <div class="row g-4">
            <div class="col-12">
                <div class="card-transparent p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="logo-circle">
                            <i class="bi bi-gear-wide-connected" style="font-size:28px; color:#dc3545"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">Bengkel Biyai Racing Shop</h3>
                            <p class="text-muted mb-0">Sistem Manajemen Keuangan sederhana untuk mencatat pemasukan &
                                pengeluaran bengkel, menghasilkan laporan, dan membantu administrasi.</p>
                        </div>
                        <div class="ms-auto text-end">
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-lg-7">
                            <h5 class="mb-3">Deskripsi Singkat</h5>
                            <p class="text-muted">Aplikasi ini dirancang untuk memudahkan pengelolaan keuangan harian
                                bengkel. Fitur-fitur utama meliputi pencatatan transaksi (pemasukan & pengeluaran),
                                pengelompokan kategori, serta pembuatan laporan harian, mingguan, dan bulanan yang dapat
                                diekspor ke PDF.</p>

                            <h5 class="mt-4">Fitur Utama</h5>
                            <div class="row g-2 mt-2">
                                <div class="col-sm-6">
                                    <div class="d-flex gap-2 align-items-start">
                                        <i class="bi bi-journal-check feature-icon mt-1"></i>
                                        <div>
                                            <strong>Catat Transaksi</strong>
                                            <div class="text-muted small">Tambah, edit, atau hapus transaksi dengan
                                                mudah.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex gap-2 align-items-start">
                                        <i class="bi bi-card-list feature-icon mt-1"></i>
                                        <div>
                                            <strong>Kategori</strong>
                                            <div class="text-muted small">Kelompokkan transaksi untuk analisis yang
                                                lebih baik.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <div class="d-flex gap-2 align-items-start">
                                        <i class="bi bi-file-earmark-text feature-icon mt-1"></i>
                                        <div>
                                            <strong>Laporan</strong>
                                            <div class="text-muted small">Laporan harian, mingguan, bulanan dan cetak
                                                PDF.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <div class="d-flex gap-2 align-items-start">
                                        <i class="bi bi-shield-lock feature-icon mt-1"></i>
                                        <div>
                                            <strong>Keamanan Sederhana</strong>
                                            <div class="text-muted small">Akses pengguna dengan sesi (login/logout).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <h5 class="mb-3">Informasi Proyek</h5>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">No bp
                                    <span class="badge bg-primary rounded-pill">22101152610489</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ilhamfahturozi
                                    <span>Sistem Informasi</span>
                                </li>
                            </ul>

                            <h5>Kontak</h5>
                            <p class="small text-muted mb-1">Jika ada pertanyaan atau ingin menyesuaikan aplikasi ini:
                            </p>
                            <p class="mb-1"><i class="bi bi-envelope"></i> <a
                                    href="mailto:ilhamfahturozi@gmail.com">ilhamfahturozi@gmail.com</a></p>
                            <p class="mb-3"><i class="bi bi-github"></i> <a href="#">https://github.com/fazeilham</a>
                            </p>

                            <div class="d-grid">
                                <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                                    Kembali ke Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-transparent p-4">
                    <h5>Catatan & Panduan Singkat</h5>
                    <ol class="small text-muted">
                        <li>Pastikan koneksi database terkonfigurasi di `DB/koneksi.php`.</li>
                        <li>Untuk mencetak laporan gunakan menu <strong>Laporan</strong> di navigasi.</li>
                    </ol>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Bengkel Biyai Racing Shop - Sistem Manajemen Keuangan</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>