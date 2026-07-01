<?php
// Helper function untuk mengecek role
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_bendahara() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'bendahara';
}

// List menu yang bisa diakses bendahara:
// - Dashboard (index.php)
// - Lihat Semua Transaksi (lihat.php)
// - Tambah Pemasukan (create_pemasukan.php)
// - Tambah Pengeluaran (create_pengeluaran.php)
// - Laporan Harian, Mingguan, Bulanan
// - Logout

// Admin bisa akses SEMUA!
?>