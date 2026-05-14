<?php
/**
 * File untuk test apakah TCPDF sudah terinstall dengan benar
 * Akses: http://localhost/financetracker/laporan/test_pdf.php
 */

echo "<h2>Test Instalasi TCPDF</h2>";

// Test 1: Cek file TCPDF
echo "<h3>1. Cek File TCPDF</h3>";
$paths = [
    __DIR__ . '/tcpdf/tcpdf.php',
    __DIR__ . '/TCPDF/tcpdf.php',
    __DIR__ . '/Tcpdf/tcpdf.php',
    __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php',
    __DIR__ . '/../vendor/autoload.php'
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        echo "✓ File ditemukan: <code>" . htmlspecialchars($path) . "</code><br>";
    } else {
        echo "✗ File tidak ditemukan: <code>" . htmlspecialchars($path) . "</code><br>";
    }
}

// Test 2: Cek class TCPDF
echo "<h3>2. Cek Class TCPDF</h3>";
$tcpdf_loaded = false;

$tcpdf_paths = [
    __DIR__ . '/tcpdf/tcpdf.php',
    __DIR__ . '/TCPDF/tcpdf.php',
    __DIR__ . '/Tcpdf/tcpdf.php',
];

foreach ($tcpdf_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $tcpdf_loaded = true;
        echo "✓ TCPDF di-load dari: <code>" . htmlspecialchars(str_replace(__DIR__, 'laporan', $path)) . "</code><br>";
        break;
    }
}

if (!$tcpdf_loaded && file_exists(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php')) {
    require_once(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php');
    $tcpdf_loaded = true;
    echo "✓ TCPDF di-load dari: <code>vendor/tecnickcom/tcpdf/tcpdf.php</code><br>";
} elseif (!$tcpdf_loaded && file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once(__DIR__ . '/../vendor/autoload.php');
    echo "✓ Autoload di-load<br>";
}

if (class_exists('TCPDF')) {
    echo "✓ Class TCPDF tersedia<br>";
    echo "✓ Versi TCPDF: " . TCPDF_STATIC::getTCPDFVersion() . "<br>";
} else {
    echo "✗ Class TCPDF TIDAK tersedia<br>";
}

// Test 3: Cek konstanta
echo "<h3>3. Cek Konstanta TCPDF</h3>";
$constants = ['PDF_PAGE_ORIENTATION', 'PDF_UNIT', 'PDF_PAGE_FORMAT'];
foreach ($constants as $const) {
    if (defined($const)) {
        echo "✓ $const = " . constant($const) . "<br>";
    } else {
        echo "✗ $const tidak terdefinisi<br>";
    }
}

// Test 4: Cek extension PHP
echo "<h3>4. Cek Extension PHP yang Diperlukan</h3>";
$extensions = ['gd', 'mbstring', 'zlib'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Extension $ext tersedia<br>";
    } else {
        echo "✗ Extension $ext TIDAK tersedia<br>";
    }
}

// Test 5: Cek permission folder
echo "<h3>5. Cek Permission Folder</h3>";
$folders = [__DIR__, __DIR__ . '/tcpdf'];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        $perms = substr(sprintf('%o', fileperms($folder)), -4);
        echo "✓ Folder: <code>" . htmlspecialchars($folder) . "</code> - Permission: $perms<br>";
    }
}

echo "<hr>";
echo "<h3>Kesimpulan</h3>";
if ($tcpdf_loaded && class_exists('TCPDF')) {
    echo "<div style='color: green; font-weight: bold;'>✓ TCPDF siap digunakan!</div>";
} else {
    echo "<div style='color: red; font-weight: bold;'>✗ TCPDF belum terinstall dengan benar.</div>";
    echo "<p>Silakan install TCPDF sesuai instruksi di README_INSTALL_PDF.md</p>";
}
?>
