# Struktur Folder Laporan yang Benar

## Struktur Folder yang Diperlukan

```
financetracker/
├── laporan/                    (atau Laporan/ - case tidak masalah di Windows)
│   ├── cetak_pdf.php          ✅ File utama untuk generate PDF
│   ├── laporan_harian.php     ✅ Halaman laporan harian
│   ├── laporan_mingguan.php    ✅ Halaman laporan mingguan
│   ├── laporan_bulanan.php    ✅ Halaman laporan bulanan
│   ├── test_pdf.php           ✅ File untuk test instalasi TCPDF
│   ├── README_INSTALL_PDF.md  ✅ Dokumentasi instalasi
│   ├── TROUBLESHOOTING.md     ✅ Dokumentasi troubleshooting
│   ├── STRUKTUR_FOLDER.md     ✅ File ini
│   │
│   └── TCPDF/                 ✅ Folder library TCPDF (case tidak masalah)
│       ├── tcpdf.php          ✅ File utama TCPDF
│       ├── config/
│       ├── fonts/
│       ├── include/
│       └── tools/
│
└── vendor/                     (opsional, jika install via Composer)
    └── tecnickcom/
        └── tcpdf/
```

## File yang Harus Ada

### 1. File Utama Laporan
- ✅ `laporan_harian.php` - Halaman laporan harian
- ✅ `laporan_mingguan.php` - Halaman laporan mingguan
- ✅ `laporan_bulanan.php` - Halaman laporan bulanan
- ✅ `cetak_pdf.php` - Generator PDF

### 2. File TCPDF
- ✅ `TCPDF/tcpdf.php` - File utama TCPDF (case tidak masalah: TCPDF, tcpdf, atau Tcpdf)

### 3. File Dokumentasi (Opsional)
- ✅ `README_INSTALL_PDF.md` - Panduan instalasi
- ✅ `TROUBLESHOOTING.md` - Troubleshooting
- ✅ `test_pdf.php` - Test instalasi

## File yang Bisa Dihapus (Duplikat)

Jika ada file berikut, bisa dihapus karena duplikat:
- ❌ `cetak.php` (duplikat dari `cetak_pdf.php`)
- ❌ `laporanharian.php` (duplikat dari `laporan_harian.php`)
- ❌ `laporanmingguan.php` (duplikat dari `laporan_mingguan.php`)
- ❌ `laporanbulanan.php` (duplikat dari `laporan_bulanan.php`)

## Catatan Penting

### 1. Case Sensitivity
- **Windows**: Case tidak masalah (`laporan` = `Laporan`)
- **Linux/Mac**: Case sensitive (`laporan` ≠ `Laporan`)
- **Solusi**: Kode sudah mendukung kedua case untuk folder TCPDF

### 2. Path TCPDF
Kode akan mencari TCPDF di lokasi berikut (berurutan):
1. `laporan/tcpdf/tcpdf.php`
2. `laporan/TCPDF/tcpdf.php`
3. `laporan/Tcpdf/tcpdf.php`
4. `vendor/tecnickcom/tcpdf/tcpdf.php` (jika install via Composer)
5. `vendor/autoload.php` (jika install via Composer)

### 3. Permission
Pastikan folder memiliki permission yang tepat:
- **Windows**: Biasanya sudah OK
- **Linux/Mac**: Minimal 755 untuk folder, 644 untuk file

## Cara Verifikasi Struktur

1. **Cek file utama:**
   ```
   laporan/laporan_harian.php
   laporan/laporan_mingguan.php
   laporan/laporan_bulanan.php
   laporan/cetak_pdf.php
   ```

2. **Cek TCPDF:**
   ```
   laporan/TCPDF/tcpdf.php
   ```
   Atau
   ```
   laporan/tcpdf/tcpdf.php
   ```

3. **Test instalasi:**
   Akses: `http://localhost/financetracker/laporan/test_pdf.php`

## Struktur Saat Ini

Berdasarkan scan folder, struktur Anda:
- ✅ Folder `laporan/` ada
- ✅ File `laporan_harian.php`, `laporan_mingguan.php`, `laporan_bulanan.php` ada
- ✅ File `cetak_pdf.php` ada
- ✅ Folder `TCPDF/` ada (huruf besar)
- ✅ File `TCPDF/tcpdf.php` ada
- ⚠️ Ada file duplikat yang bisa dihapus

**Kesimpulan:** Struktur folder sudah benar! ✅

File duplikat bisa dihapus untuk kebersihan, tapi tidak mempengaruhi fungsi.
