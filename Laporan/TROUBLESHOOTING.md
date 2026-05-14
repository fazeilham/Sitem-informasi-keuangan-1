# Troubleshooting Error PDF

## Error yang Sering Terjadi

### 1. Error: "Library TCPDF Belum Terinstall!"

**Penyebab:** Folder `tcpdf` tidak ditemukan di folder `laporan/`

**Solusi:**
1. Download TCPDF dari: https://github.com/tecnickcom/TCPDF
2. Extract file ZIP
3. Copy folder `tcpdf` ke dalam folder `laporan/`
4. Struktur folder harus seperti ini:
   ```
   laporan/
   ├── tcpdf/
   │   ├── tcpdf.php
   │   └── ...
   ├── cetak_pdf.php
   └── ...
   ```

**Atau install via Composer:**
```bash
composer require tecnickcom/tcpdf
```

---

### 2. Error: "Class 'TCPDF' not found"

**Penyebab:** File TCPDF tidak ter-load dengan benar

**Solusi:**
1. Pastikan file `tcpdf/tcpdf.php` ada
2. Cek permission folder (harus readable)
3. Cek path file dengan mengakses: `laporan/test_pdf.php`

---

### 3. Error: "Error query: ..."

**Penyebab:** Query database gagal

**Solusi:**
1. Cek koneksi database
2. Pastikan tabel `transaksi` sudah ada
3. Cek apakah kolom yang diperlukan sudah ada (unit_keterangan, jasa_detail, barang_sparepart)

**Jalankan query ini untuk menambahkan kolom:**
```sql
ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) NULL AFTER kategori;
ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT NULL AFTER unit_keterangan;
ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT NULL AFTER jasa_detail;
```

---

### 4. Error: "Error saat membuat PDF!"

**Penyebab:** Ada masalah saat generate PDF

**Solusi:**
1. Cek error log PHP
2. Pastikan extension PHP yang diperlukan sudah aktif:
   - `gd` (untuk image processing)
   - `mbstring` (untuk string handling)
   - `zlib` (untuk compression)
3. Cek memory limit PHP (minimal 128MB)

**Aktifkan extension di php.ini:**
```ini
extension=gd
extension=mbstring
extension=zlib
```

---

### 5. PDF Kosong atau Tidak Muncul

**Penyebab:** 
- Tidak ada data transaksi pada periode yang dipilih
- Error saat generate HTML

**Solusi:**
1. Pastikan ada data transaksi pada periode yang dipilih
2. Cek apakah query mengembalikan data
3. Cek error log browser (F12 > Console)

---

### 6. Error: "Undefined constant PDF_PAGE_ORIENTATION"

**Penyebab:** Konstanta TCPDF tidak terdefinisi

**Solusi:**
- File sudah diperbaiki untuk mendefinisikan konstanta secara manual
- Pastikan TCPDF ter-load dengan benar

---

## Cara Test Instalasi

Akses file test: `http://localhost/financetracker/laporan/test_pdf.php`

File ini akan menampilkan:
- Status file TCPDF
- Status class TCPDF
- Status konstanta
- Status extension PHP
- Permission folder

---

## Alternatif Library PDF

Jika TCPDF bermasalah, bisa menggunakan library lain:

### mPDF
```bash
composer require mpdf/mpdf
```

### DomPDF
```bash
composer require dompdf/dompdf
```

---

## Tips

1. **Selalu cek error log PHP** untuk detail error
2. **Gunakan file test_pdf.php** untuk diagnose masalah
3. **Pastikan semua extension PHP aktif**
4. **Cek permission folder** (harus readable dan writable)
5. **Pastikan memory_limit PHP cukup** (minimal 128MB)

---

## Support

Jika masih ada masalah:
1. Cek error log PHP
2. Cek error log browser (F12)
3. Gunakan file `test_pdf.php` untuk diagnose
4. Pastikan semua requirement terpenuhi
