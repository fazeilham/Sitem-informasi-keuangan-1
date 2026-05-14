<?php
session_start();
require_once '../DB/koneksi.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek dan include TCPDF
$tcpdf_loaded = false;

// Coba load dari folder laporan (case-insensitive untuk Windows compatibility)
$tcpdf_paths = [
    __DIR__ . '/tcpdf/tcpdf.php',      // lowercase
    __DIR__ . '/TCPDF/tcpdf.php',      // uppercase
    __DIR__ . '/Tcpdf/tcpdf.php',     // mixed case
];

foreach ($tcpdf_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $tcpdf_loaded = true;
        break;
    }
}

// Coba load dari vendor (jika install via composer)
if (!$tcpdf_loaded && file_exists(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php')) {
    require_once(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php');
    $tcpdf_loaded = true;
}

// Coba load dari root vendor
if (!$tcpdf_loaded && file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once(__DIR__ . '/../vendor/autoload.php');
    if (class_exists('TCPDF')) {
        $tcpdf_loaded = true;
    }
}

// Jika TCPDF belum terinstall, tampilkan error
if (!$tcpdf_loaded || !class_exists('TCPDF')) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - TCPDF Required</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; }
            code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-danger">
                <h4><i class="bi bi-exclamation-triangle"></i> Library TCPDF Belum Terinstall!</h4>
                <p>Untuk menggunakan fitur cetak PDF, Anda perlu menginstall library TCPDF terlebih dahulu.</p>
                <hr>
                <h5>Cara Install:</h5>
                <ol>
                    <li>Download TCPDF dari: <a href="https://github.com/tecnickcom/TCPDF" target="_blank">https://github.com/tecnickcom/TCPDF</a></li>
                    <li>Extract file dan copy folder <code>tcpdf</code> ke folder <code>laporan/</code></li>
                    <li>Atau install via Composer: <code>composer require tecnickcom/tcpdf</code></li>
                </ol>
                <p class="mb-0"><strong>Alternatif:</strong> Gunakan library mPDF atau DomPDF yang lebih mudah diinstall.</p>
            </div>
            <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
        </div>
    </body>
    </html>
    ');
}

// Ambil parameter
$type = isset($_GET['type']) ? $_GET['type'] : 'harian';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Setup query berdasarkan type
switch($type) {
    case 'harian':
        $query = "SELECT * FROM transaksi WHERE tanggal = '$tanggal' ORDER BY created_at DESC";
        $query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan' AND tanggal = '$tanggal'";
        $query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran' AND tanggal = '$tanggal'";
        $periode = date('d F Y', strtotime($tanggal));
        $filename = 'Laporan_Harian_' . date('Y-m-d', strtotime($tanggal)) . '.pdf';
        break;
        
    case 'mingguan':
        $query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC, created_at DESC";
        $query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $periode = date('d F Y', strtotime($tanggal_awal)) . ' - ' . date('d F Y', strtotime($tanggal_akhir));
        $filename = 'Laporan_Mingguan_' . date('Y-m-d', strtotime($tanggal_awal)) . '.pdf';
        break;
        
    case 'bulanan':
        $tanggal_awal = $bulan . '-01';
        $tanggal_akhir = date('Y-m-t', strtotime($tanggal_awal));
        $query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC, created_at DESC";
        $query_pemasukan = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pemasukan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $query_pengeluaran = "SELECT SUM(jumlah) as total FROM transaksi WHERE jenis = 'pengeluaran' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $periode = date('F Y', strtotime($tanggal_awal));
        $filename = 'Laporan_Bulanan_' . $bulan . '.pdf';
        break;
        
    default:
        die('Type laporan tidak valid!');
}

// Eksekusi query dengan error handling
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die('Error query: ' . mysqli_error($koneksi));
}

$result_pemasukan = mysqli_query($koneksi, $query_pemasukan);
if (!$result_pemasukan) {
    die('Error query pemasukan: ' . mysqli_error($koneksi));
}

$result_pengeluaran = mysqli_query($koneksi, $query_pengeluaran);
if (!$result_pengeluaran) {
    die('Error query pengeluaran: ' . mysqli_error($koneksi));
}

$total_pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;
$total_pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
$total_transaksi = mysqli_num_rows($result);

// Create PDF
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Bengkel Biyai Racing Shop', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 10, 'Laporan Keuangan', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Define constants if not defined
if (!defined('PDF_PAGE_ORIENTATION')) {
    define('PDF_PAGE_ORIENTATION', 'P'); // Portrait
}
if (!defined('PDF_UNIT')) {
    define('PDF_UNIT', 'mm');
}
if (!defined('PDF_PAGE_FORMAT')) {
    define('PDF_PAGE_FORMAT', 'A4');
}

// Create PDF object
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Bengkel Biyai Racing Shop');
$pdf->SetAuthor('Bengkel Biyai Racing Shop');
$pdf->SetTitle('Laporan Keuangan');
$pdf->SetSubject('Laporan Keuangan');

// Remove default header/footer
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

// Set margins
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Content
$html = '
<style>
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th { background-color: #667eea; color: white; padding: 8px; text-align: left; font-weight: bold; }
    td { padding: 6px; border: 1px solid #ddd; }
    .header-info { background-color: #f0f0f0; padding: 10px; margin-bottom: 10px; }
    .stat-box { background-color: #e7f3ff; padding: 10px; margin: 10px 0; border-left: 4px solid #667eea; }
    .pemasukan { color: #28a745; font-weight: bold; }
    .pengeluaran { color: #dc3545; font-weight: bold; }
</style>

<div class="header-info">
    <strong>Periode:</strong> ' . $periode . '<br>
    <strong>Tanggal Cetak:</strong> ' . date('d F Y H:i:s') . '
</div>

<div class="stat-box">
    <strong>RINGKASAN</strong><br>
    Total Transaksi: ' . number_format($total_transaksi, 0, ',', '.') . '<br>
    Total Pemasukan: Rp ' . number_format($total_pemasukan, 0, ',', '.') . '<br>
    Total Pengeluaran: Rp ' . number_format($total_pengeluaran, 0, ',', '.') . '<br>
    Saldo: Rp ' . number_format($saldo, 0, ',', '.') . '
</div>

<table>
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="12%">Tanggal</th>
            <th width="10%">Jenis</th>
            <th width="15%">Kategori</th>
            <th width="15%">Unit/Keterangan</th>
            <th width="15%">Jasa & Detail</th>
            <th width="15%">Sparepart</th>
            <th width="13%">Nominal</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
if ($total_transaksi > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $unit_keterangan = !empty($row['unit_keterangan']) ? $row['unit_keterangan'] : '-';
        $jasa_detail = !empty($row['jasa_detail']) ? $row['jasa_detail'] : '-';
        $barang_sparepart = !empty($row['barang_sparepart']) ? $row['barang_sparepart'] : '-';
        $jenis_class = $row['jenis'] == 'pemasukan' ? 'pemasukan' : 'pengeluaran';
        $jenis_sign = $row['jenis'] == 'pemasukan' ? '+' : '-';
        
        $html .= '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . date('d/m/Y', strtotime($row['tanggal'])) . '</td>
                <td>' . ucfirst($row['jenis']) . '</td>
                <td>' . htmlspecialchars($row['kategori']) . '</td>
                <td>' . htmlspecialchars($unit_keterangan) . '</td>
                <td>' . htmlspecialchars($jasa_detail) . '</td>
                <td>' . htmlspecialchars($barang_sparepart) . '</td>
                <td class="' . $jenis_class . '">' . $jenis_sign . 'Rp ' . number_format($row['jumlah'], 0, ',', '.') . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data transaksi pada periode ini</td></tr>';
}

$html .= '
    </tbody>
</table>';

// Print text using writeHTMLCell()
try {
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Close and output PDF document
    $pdf->Output($filename, 'D'); // 'D' untuk download, 'I' untuk preview di browser
} catch (Exception $e) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - PDF Generation</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-danger">
                <h4>Error saat membuat PDF!</h4>
                <p><strong>Pesan Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Silakan coba lagi atau hubungi administrator.</p>
            </div>
            <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
        </div>
    </body>
    </html>
    ');
}

exit();
?>