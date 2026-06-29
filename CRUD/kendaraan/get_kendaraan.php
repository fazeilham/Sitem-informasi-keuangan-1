<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../../DB/koneksi.php';

$pelanggan_id = isset($_GET['pelanggan_id']) ? intval($_GET['pelanggan_id']) : 0;

if ($pelanggan_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter pelanggan_id tidak valid.', 'data' => []]);
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id, no_plat, merek, tahun FROM kendaraan WHERE pelanggan_id = ? ORDER BY no_plat ASC");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan query.', 'data' => []]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $pelanggan_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => $row['id'],
        'no_plat' => $row['no_plat'],
        'merek' => $row['merek'],
        'tahun' => $row['tahun'],
    ];
}

mysqli_stmt_close($stmt);

echo json_encode(['status' => 'success', 'data' => $data]);
