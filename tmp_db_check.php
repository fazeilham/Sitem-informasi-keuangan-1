<?php
require_once __DIR__ . '/DB/koneksi.php';
$result = mysqli_query($koneksi, 'SHOW TABLES');
if (!$result) {
    echo 'ERROR: ' . mysqli_error($koneksi) . PHP_EOL;
    exit(1);
}
while ($row = mysqli_fetch_row($result)) {
    echo $row[0] . PHP_EOL;
}
echo '---' . PHP_EOL;
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM sparepart");
if (!$result) {
    echo 'SPAREPART ERROR: ' . mysqli_error($koneksi) . PHP_EOL;
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo 'sparepart.' . $row['Field'] . ' ' . $row['Type'] . PHP_EOL;
    }
}
 echo '---' . PHP_EOL;
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM detail_transaksi");
if (!$result) {
    echo 'DETAIL_TRANSAKSI ERROR: ' . mysqli_error($koneksi) . PHP_EOL;
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo 'detail_transaksi.' . $row['Field'] . ' ' . $row['Type'] . PHP_EOL;
    }
}
