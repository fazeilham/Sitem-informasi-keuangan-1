# Struktur Database Lengkap - Bengkel Biyai Racing Shop

## Struktur Database Saat Ini

### Database: `financetracker_bengkel`

**Total Tabel: 2 tabel**

1. ✅ `users` - Data user/admin
2. ✅ `transaksi` - Data transaksi keuangan (termasuk barang & jasa)

---

## Penjelasan: Mengapa Tidak Ada Tabel Terpisah?

### ❌ Tabel Barang - TIDAK ADA
**Alasan:** Data barang/sparepart disimpan langsung di tabel `transaksi` pada kolom `barang_sparepart` (TEXT)

**Cara kerja saat ini:**
- Setiap transaksi bisa memiliki data barang/sparepart
- Disimpan sebagai teks bebas (contoh: "Oli mesin 1L x Rp 50.000, Filter udara x Rp 25.000")
- Fleksibel untuk berbagai jenis barang

### ❌ Tabel Jasa - TIDAK ADA
**Alasan:** Data jasa disimpan langsung di tabel `transaksi` pada kolom `jasa_detail` (TEXT)

**Cara kerja saat ini:**
- Setiap transaksi bisa memiliki data jasa
- Disimpan sebagai teks bebas (contoh: "Service berkala, Ganti oli, Tune up mesin")
- Fleksibel untuk berbagai jenis jasa

### ❌ Tabel Laporan - TIDAK ADA
**Alasan:** Laporan di-generate langsung dari tabel `transaksi` berdasarkan filter tanggal/periode

**Cara kerja saat ini:**
- Laporan harian: Query `SELECT * FROM transaksi WHERE tanggal = '...'`
- Laporan mingguan: Query `SELECT * FROM transaksi WHERE tanggal BETWEEN '...' AND '...'`
- Laporan bulanan: Query `SELECT * FROM transaksi WHERE tanggal BETWEEN '...' AND '...'`
- Tidak perlu menyimpan laporan karena bisa di-generate kapan saja

---

## Struktur Tabel `transaksi` (Saat Ini)

```sql
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    keterangan TEXT,
    unit_keterangan VARCHAR(255) NULL,      -- Unit/Keterangan
    jasa_detail TEXT NULL,                   -- Data jasa (disimpan sebagai TEXT)
    barang_sparepart TEXT NULL,              -- Data barang (disimpan sebagai TEXT)
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Data barang dan jasa disimpan sebagai TEXT di kolom:**
- `barang_sparepart` - untuk data barang/sparepart
- `jasa_detail` - untuk data jasa

---

## Opsi: Normalisasi Database (Jika Diperlukan)

Jika Anda ingin membuat sistem yang lebih terstruktur dengan master data, bisa dibuat tabel tambahan:

### Opsi 1: Tabel Master Barang

```sql
-- Tabel Master Barang/Sparepart
CREATE TABLE master_barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) UNIQUE,
    nama_barang VARCHAR(255) NOT NULL,
    harga DECIMAL(15, 2) NOT NULL,
    satuan VARCHAR(50),
    stok INT DEFAULT 0,
    kategori VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Detail Barang di Transaksi
CREATE TABLE transaksi_barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT,
    barang_id INT,
    quantity INT NOT NULL,
    harga_satuan DECIMAL(15, 2) NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (barang_id) REFERENCES master_barang(id)
);
```

### Opsi 2: Tabel Master Jasa

```sql
-- Tabel Master Jasa
CREATE TABLE master_jasa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jasa VARCHAR(50) UNIQUE,
    nama_jasa VARCHAR(255) NOT NULL,
    harga DECIMAL(15, 2) NOT NULL,
    kategori VARCHAR(100),
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Detail Jasa di Transaksi
CREATE TABLE transaksi_jasa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT,
    jasa_id INT,
    harga DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (jasa_id) REFERENCES master_jasa(id)
);
```

### Opsi 3: Tabel Laporan (Opsional - Tidak Disarankan)

```sql
-- Tabel untuk menyimpan history laporan (opsional)
CREATE TABLE laporan_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jenis_laporan ENUM('harian', 'mingguan', 'bulanan') NOT NULL,
    periode VARCHAR(50) NOT NULL,
    tanggal_awal DATE,
    tanggal_akhir DATE,
    total_pemasukan DECIMAL(15, 2),
    total_pengeluaran DECIMAL(15, 2),
    saldo DECIMAL(15, 2),
    file_pdf VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Catatan:** Tabel laporan biasanya TIDAK diperlukan karena laporan bisa di-generate kapan saja dari data transaksi.

---

## Perbandingan: Struktur Saat Ini vs Normalisasi

### Struktur Saat Ini (Simple)
✅ **Kelebihan:**
- Sederhana dan mudah digunakan
- Fleksibel untuk berbagai jenis barang/jasa
- Tidak perlu maintenance master data
- Cocok untuk bengkel kecil-menengah

❌ **Kekurangan:**
- Tidak ada master data barang/jasa
- Tidak bisa tracking stok barang
- Tidak bisa analisis per barang/jasa
- Data bisa duplikat (contoh: "Oli mesin" ditulis berbeda-beda)

### Struktur Normalisasi (Advanced)
✅ **Kelebihan:**
- Ada master data barang/jasa
- Bisa tracking stok
- Bisa analisis per barang/jasa
- Data konsisten dan terstruktur
- Cocok untuk bengkel besar

❌ **Kekurangan:**
- Lebih kompleks
- Perlu maintenance master data
- Perlu input barang/jasa ke master dulu
- Lebih banyak tabel dan relasi

---

## Rekomendasi

### Untuk Skripsi/Project Kecil:
✅ **Gunakan struktur saat ini (2 tabel)**
- Sudah cukup untuk kebutuhan bengkel
- Lebih mudah diimplementasikan
- Fokus ke fitur utama (CRUD, laporan, PDF)

### Untuk Production/Bengkel Besar:
✅ **Pertimbangkan normalisasi**
- Buat tabel master barang dan jasa
- Tracking stok barang
- Analisis lebih detail

---

## Kesimpulan

**Saat ini:**
- ❌ Tidak ada tabel `barang` - data disimpan di `transaksi.barang_sparepart`
- ❌ Tidak ada tabel `jasa` - data disimpan di `transaksi.jasa_detail`
- ❌ Tidak ada tabel `laporan` - laporan di-generate dari `transaksi`

**Struktur saat ini sudah cukup untuk:**
- ✅ Input transaksi dengan barang dan jasa
- ✅ Generate laporan harian/mingguan/bulanan
- ✅ Cetak PDF laporan
- ✅ CRUD transaksi lengkap

**Jika perlu normalisasi, bisa ditambahkan tabel:**
- `master_barang` - untuk master data barang
- `master_jasa` - untuk master data jasa
- `transaksi_barang` - untuk detail barang di transaksi
- `transaksi_jasa` - untuk detail jasa di transaksi

---

## Query untuk Cek Struktur Saat Ini

```sql
-- Lihat semua tabel
SHOW TABLES;

-- Lihat struktur tabel transaksi
DESCRIBE transaksi;

-- Lihat contoh data barang dan jasa
SELECT 
    id,
    tanggal,
    kategori,
    unit_keterangan,
    jasa_detail,
    barang_sparepart,
    jumlah
FROM transaksi
LIMIT 5;
```
