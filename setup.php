<?php
require_once 'DB/koneksi.php';

// Buat tabel jika belum ada
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_transaksi_table = "CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    keterangan TEXT,
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

mysqli_query($koneksi, $create_users_table);
mysqli_query($koneksi, $create_transaksi_table);

// Tambahkan kolom baru jika belum ada
$check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE 'unit_keterangan'");
if (mysqli_num_rows($check_columns) == 0) {
    mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) AFTER kategori");
    mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT AFTER unit_keterangan");
    mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT AFTER jasa_detail");
}

// Hash password untuk admin123
$password_hash = password_hash('admin123', PASSWORD_DEFAULT);

// Hapus user admin lama jika ada
mysqli_query($koneksi, "DELETE FROM users WHERE username = 'admin'");

// Insert user admin baru dengan password yang benar
$insert_user = "INSERT INTO users (username, password, nama) VALUES ('admin', '$password_hash', 'Administrator')";

if (mysqli_query($koneksi, $insert_user)) {
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Setup Berhasil</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .success-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                padding: 40px;
                max-width: 500px;
                text-align: center;
            }
            .success-icon {
                font-size: 4rem;
                color: #28a745;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class='success-card'>
            <i class='bi bi-check-circle-fill success-icon'></i>
            <h2>Setup Berhasil!</h2>
            <p>User admin telah dibuat dengan password yang benar.</p>
            <hr>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> admin123</p>
            <hr>
            <a href='login.php' class='btn btn-primary btn-lg'>
                <i class='bi bi-box-arrow-in-right'></i> Kembali ke Login
            </a>
            <br><br>
            <small class='text-muted'>Hapus file setup.php setelah setup selesai untuk keamanan</small>
        </div>
    </body>
    </html>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>