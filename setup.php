<?php
require_once 'DB/koneksi.php';

// Buat tabel users
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_users_table);

// Buat tabel kategori
$create_kategori_table = "CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_kategori_table);

// Buat tabel pelanggan
$create_pelanggan_table = "CREATE TABLE IF NOT EXISTS pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_pelanggan_table);

// Buat tabel kendaraan
$create_kendaraan_table = "CREATE TABLE IF NOT EXISTS kendaraan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    no_plat VARCHAR(20) NOT NULL,
    merek VARCHAR(50),
    tipe VARCHAR(50),
    tahun VARCHAR(4),
    warna VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_kendaraan_table);

// Buat tabel sparepart
$create_sparepart_table = "CREATE TABLE IF NOT EXISTS sparepart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kode_part VARCHAR(50),
    satuan VARCHAR(20),
    harga_beli DECIMAL(15,2) DEFAULT 0,
    harga_jual DECIMAL(15,2) DEFAULT 0,
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_sparepart_table);

// Buat tabel transaksi
$create_transaksi_table = "CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    kategori_id INT,
    pelanggan_id INT,
    kendaraan_id INT,
    unit_keterangan VARCHAR(255),
    jasa_detail TEXT,
    barang_sparepart TEXT,
    keterangan TEXT,
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    user_id INT,
    metode_pembayaran VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_transaksi_table);

// Buat tabel detail_transaksi
$create_detail_transaksi_table = "CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    sparepart_id INT,
    nama_item VARCHAR(100),
    qty INT DEFAULT 0,
    harga_satuan DECIMAL(15,2) DEFAULT 0,
    subtotal DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (sparepart_id) REFERENCES sparepart(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
mysqli_query($koneksi, $create_detail_transaksi_table);

// Insert default kategori jika kosong
$check_kategori = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori");
$row = mysqli_fetch_assoc($check_kategori);
if ($row['total'] == 0) {
    $default_kategori = [
        ['Service', 'pemasukan'],
        ['Sparepart', 'pemasukan'],
        ['Jasa', 'pemasukan'],
        ['Oli', 'pemasukan'],
        ['Ban', 'pemasukan'],
        ['Gaji Karyawan', 'pengeluaran'],
        ['Beli Sparepart', 'pengeluaran'],
        ['Listrik', 'pengeluaran'],
        ['Sewa Tempat', 'pengeluaran'],
        ['Lain-lain', 'pemasukan'],
        ['Lain-lain', 'pengeluaran']
    ];
    foreach ($default_kategori as $kat) {
        $nama = mysqli_real_escape_string($koneksi, $kat[0]);
        $jenis = $kat[1];
        mysqli_query($koneksi, "INSERT INTO kategori (nama, jenis) VALUES ('$nama', '$jenis')");
    }
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
            <p>Semua tabel dan data default telah dibuat.</p>
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