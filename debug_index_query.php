<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'DB/koneksi.php';

echo "<h2>Debug Query Index.php</h2>";

// 1. Cek semua tabel yang ada
echo "<h3>1. Tabel di Database</h3>";
$tables = mysqli_query($koneksi, "SHOW TABLES");
while ($t = mysqli_fetch_row($tables)) {
    echo "- " . $t[0] . "<br>";
}

echo "<hr>";

// 2. Cek kolom di transaksi
echo "<h3>2. Kolom di Tabel Transaksi</h3>";
$cols = mysqli_query($koneksi, "SHOW COLUMNS FROM transaksi");
echo "<ul>";
while ($c = mysqli_fetch_assoc($cols)) {
    echo "<li><strong>" . $c['Field'] . "</strong> - " . $c['Type'] . "</li>";
}
echo "</ul>";

echo "<hr>";

// 3. Test query transaksi
echo "<h3>3. Test Query</h3>";
$query = "SELECT t.*, k.nama as nama_kategori, p.nama as nama_pelanggan, v.no_plat 
          FROM transaksi t 
          LEFT JOIN kategori k ON t.kategori_id = k.id 
          LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
          LEFT JOIN kendaraan v ON t.kendaraan_id = v.id 
          ORDER BY t.tanggal DESC, t.created_at DESC LIMIT 10";

echo "Query: " . htmlspecialchars($query) . "<br><br>";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo "<span style='color:red; font-weight: bold;'>❌ Query Gagal!</span><br>";
    echo "Error: " . mysqli_error($koneksi) . "<br><br>";
    
    // Coba query sederhana
    echo "<h4>Coba Query Sederhana:</h4>";
    $simple_query = "SELECT * FROM transaksi";
    $simple_result = mysqli_query($koneksi, $simple_query);
    if ($simple_result) {
        echo "<span style='color:green'>✅ Query sederhana berhasil!</span><br>";
        echo "Jumlah baris: " . mysqli_num_rows($simple_result) . "<br>";
    } else {
        echo "<span style='color:red'>❌ Query sederhana juga gagal: " . mysqli_error($koneksi) . "</span><br>";
    }
} else {
    echo "<span style='color:green; font-weight: bold;'>✅ Query Berhasil!</span><br>";
    echo "Jumlah baris: " . mysqli_num_rows($result) . "<br>";
}

echo "<hr>";
echo "<a href='index.php'>Ke Dashboard</a>";
?>