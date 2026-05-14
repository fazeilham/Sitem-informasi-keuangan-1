# Instalasi Library PDF untuk Cetak Laporan

## Opsi 1: Menggunakan TCPDF (Recommended)

### Cara Install TCPDF:

1. **Download TCPDF**
   - Kunjungi: https://github.com/tecnickcom/TCPDF
   - Atau download langsung: https://github.com/tecnickcom/TCPDF/archive/main.zip

2. **Extract dan Copy**
   - Extract file ZIP yang sudah didownload
   - Copy folder `tcpdf` ke dalam folder `laporan/`
   - Struktur folder harus seperti ini:
     ```
     laporan/
     ├── tcpdf/
     │   ├── tcpdf.php
     │   └── ...
     ├── cetak_pdf.php
     └── ...
     ```

3. **Selesai!**
   - File `cetak_pdf.php` sudah siap digunakan

---

## Opsi 2: Menggunakan Composer

Jika project Anda menggunakan Composer:

```bash
composer require tecnickcom/tcpdf
```

Kemudian update file `cetak_pdf.php`:
```php
require_once('../vendor/autoload.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
```

---

## Opsi 3: Menggunakan mPDF (Alternatif)

mPDF lebih mudah digunakan dan memiliki dukungan CSS yang lebih baik.

### Install via Composer:
```bash
composer require mpdf/mpdf
```

### Update cetak_pdf.php untuk menggunakan mPDF:
```php
require_once('../vendor/autoload.php');

use Mpdf\Mpdf;

$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output($filename, 'D');
```

---

## Opsi 4: Menggunakan DomPDF (Alternatif)

DomPDF juga populer dan mudah digunakan.

### Install via Composer:
```bash
composer require dompdf/dompdf
```

### Update cetak_pdf.php untuk menggunakan DomPDF:
```php
require_once('../vendor/autoload.php');

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($filename);
```

---

## Troubleshooting

### Error: "Class 'TCPDF' not found"
- Pastikan folder `tcpdf` sudah ada di dalam folder `laporan/`
- Pastikan file `tcpdf/tcpdf.php` ada dan bisa diakses

### Error: "Permission denied"
- Pastikan folder `laporan/` memiliki permission yang tepat (755 atau 777)

### PDF tidak muncul
- Cek error log PHP
- Pastikan extension PHP yang diperlukan sudah aktif (gd, mbstring)

---

## Catatan

- TCPDF adalah library yang paling populer dan stabil
- File `cetak_pdf.php` sudah dibuat untuk menggunakan TCPDF
- Jika ingin menggunakan library lain, sesuaikan kode di `cetak_pdf.php`
