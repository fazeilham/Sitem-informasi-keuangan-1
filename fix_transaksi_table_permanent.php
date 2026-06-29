<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'DB/koneksi.php';

echo "<h2>Fix Tabel Transaksi - Permanent</h2>";

// Daftar kolom yang harus ada
$required_columns = [
    'kategori_id' => "INT NULL AFTER kategori",
    'pelanggan_id' => "INT NULL AFTER kategori_id",
    'kendaraan_id' => "INT NULL AFTER pelanggan_id",
    'metode_pembayaran' => "VARCHAR(50) NULL AFTER keterangan"
];

foreach ($required_columns as $col_name => $col_def) {
    echo "<h3>Memeriksa kolom '$col_name'...</h3>";
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi LIKE '$col_name'");
    if (mysqli_num_rows($check) > 0) {
        echo "<span style='color:green'>✅ Kolom '$col_name' sudah ada!</span><br>";
    } else {
        $sql = "ALTER TABLE transaksi ADD COLUMN $col_name $col_def";
        if (mysqli_query($koneksi, $sql)) {
            echo "<span style='color:green'>✅ Kolom '$col_name' berhasil ditambahkan!</span><br>";
        } else {
            echo "<span style='color:red'>❌ Gagal: " . mysqli_error($koneksi) . "</span><br>";
        }
    }
}

echo "<hr>";
echo "<h3>Test Query Index.php...</h3>";
$query_test = "SELECT t.*, k.nama as nama_kategori, p.nama as nama_pelanggan, v.no_plat 
                FROM transaksi t 
                LEFT JOIN kategori k ON t.kategori_id = k.id 
                LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
                LEFT JOIN kendaraan v ON t.kendaraan_id = v.id 
                ORDER BY t.tanggal DESC, t.created_at DESC LIMIT 10";
$result_test = mysqli_query($koneksi, $query_test);

if ($result_test) {
    echo "<span style='color:green; font-size: 20px; font-weight: bold;'>🎉 SEMUA SUKSES! Dashboard siap!</span><br><br>";
} else {
    echo "<span style='color:red'>❌ Gagal: " . mysqli_error($koneksi) . "</span><br><br>";
}

echo "<a href='index.php'>Ke Dashboard</a> | ";
echo "<a href='CRUD/sparepart/index.php'>Master Sparepart</a>";
?>