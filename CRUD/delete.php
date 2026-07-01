<?php
session_start();
require_once '../DB/koneksi.php';
require_once '../helpers.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$success = false;
$error = '';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Verifikasi bahwa transaksi ada dan milik user yang login (opsional, untuk keamanan)
    $check_query = "SELECT * FROM transaksi WHERE id = $id";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Hapus transaksi
        $delete_query = "DELETE FROM transaksi WHERE id = $id";
        
        if (mysqli_query($koneksi, $delete_query)) {
            $success = true;
        } else {
            $error = "Error: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Transaksi tidak ditemukan!";
    }
} else {
    $error = "ID transaksi tidak valid!";
}

// Redirect berdasarkan hasil
if ($success) {
    header("Location: lihat.php?success=Data transaksi berhasil dihapus!");
} else {
    header("Location: lihat.php?error=" . urlencode($error));
}
exit();
?>
