<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'DB/koneksi.php';

echo "<h2>Memperbaiki Database...</h2>";

// 1. Tabel kategori
echo "<h3>1. Memeriksa tabel kategori...</h3>";
$create_kategori = "CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if (mysqli_query($koneksi, $create_kategori)) {
    echo "✓ Tabel kategori OK<br>";
    
    // Insert default kategori jika kosong
    $check = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori");
    $row = mysqli_fetch_assoc($check);
    if ($row['total'] == 0) {
        $defaults = [
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
        foreach ($defaults as $d) {
            $n = mysqli_real_escape_string($koneksi, $d[0]);
            $j = $d[1];
            mysqli_query($koneksi, "INSERT INTO kategori (nama, jenis) VALUES ('$n', '$j')");
        }
        echo "✓ Default kategori ditambahkan<br>";
    }
} else {
    echo "✗ Error: " . mysqli_error($koneksi) . "<br>";
}

// 2. Tabel pelanggan
echo "<h3>2. Memeriksa tabel pelanggan...</h3>";
$create_pelanggan = "CREATE TABLE IF NOT EXISTS pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if (mysqli_query($koneksi, $create_pelanggan)) {
    echo "✓ Tabel pelanggan OK<br>";
} else {
    echo "✗ Error: " . mysqli_error($koneksi) . "<br>";
}

// 3. Tabel kendaraan
echo "<h3>3. Memeriksa tabel kendaraan...</h3>";
$create_kendaraan = "CREATE TABLE IF NOT EXISTS kendaraan (
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
if (mysqli_query($koneksi, $create_kendaraan)) {
    echo "✓ Tabel kendaraan OK<br>";
} else {
    echo "✗ Error: " . mysqli_error($koneksi) . "<br>";
}

// 4. Tabel sparepart
echo "<h3>4. Memeriksa tabel sparepart...</h3>";
$create_sparepart = "CREATE TABLE IF NOT EXISTS sparepart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kode_part VARCHAR(50),
    satuan VARCHAR(20),
    harga_beli DECIMAL(15, 2) DEFAULT 0,
    harga_jual DECIMAL(15, 2) DEFAULT 0,
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if (mysqli_query($koneksi, $create_sparepart)) {
    echo "✓ Tabel sparepart OK<br>";
} else {
    echo "✗ Error: " . mysqli_error($koneksi) . "<br>";
}

// 5. Tabel detail_transaksi
echo "<h3>5. Memeriksa tabel detail_transaksi...</h3>";
$create_detail = "CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    sparepart_id INT,
    nama_item VARCHAR(100),
    qty INT DEFAULT 0,
    harga_satuan DECIMAL(15, 2) DEFAULT 0,
    subtotal DECIMAL(15, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (sparepart_id) REFERENCES sparepart(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if (mysqli_query($koneksi, $create_detail)) {
    echo "✓ Tabel detail_transaksi OK<br>";
} else {
    echo "✗ Error: " . mysqli_error($koneksi) . "<br>";
}

// 6. Memperbarui tabel transaksi (tambah kolom yang hilang)
echo "<h3>6. Memperbarui tabel transaksi...</h3>";
$columns_to_add = [
    'pelanggan_id' => "INT AFTER tanggal",
    'kategori_id' => "INT AFTER pelanggan_id",
    'kendaraan_id' => "INT AFTER kategori_id",
    'metode_pembayaran' => "VARCHAR(50) AFTER keterangan"
];

foreach ($columns_to_add as $col => $def) {
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE '$col'");
    if (mysqli_num_rows($check) == 0) {
        if (mysqli_query($koneksi, "ALTER TABLE transaksi ADD COLUMN $col $def")) {
            echo "✓ Kolom $col ditambahkan<br>";
        } else {
            echo "✗ Gagal menambah $col: " . mysqli_error($koneksi) . "<br>";
        }
    } else {
        echo "✓ Kolom $col sudah ada<br>";
    }
}

// 7. Pastikan user admin ada
echo "<h3>7. Memeriksa user admin...</h3>";
$check_admin = mysqli_query($koneksi, "SELECT * FROM users WHERE username = 'admin'");
if (mysqli_num_rows($check_admin) == 0) {
    $pass_hash = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($koneksi, "INSERT INTO users (username, password, nama) VALUES ('admin', '$pass_hash', 'Administrator')");
    echo "✓ User admin dibuat<br>";
} else {
    echo "✓ User admin sudah ada<br>";
}

echo "<hr>";
echo "<h2 style='color: green;'>✓ SEMUA PERBAIKAN BERHASIL!</h2>";
echo "<p><a href='index.php'>← Ke Dashboard</a></p>";
echo "<p><small>Hapus file fix_all_database.php setelah selesai!</small></p>";
?>