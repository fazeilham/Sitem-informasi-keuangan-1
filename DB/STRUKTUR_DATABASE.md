# Struktur Database - Bengkel Biyai Racing Shop

## Database: `financetracker_bengkel`

Database ini memiliki **2 tabel**:

---

## 1. Tabel `users` (Tabel User/Admin)

**Fungsi:** Menyimpan data user/admin yang bisa login ke sistem

### Struktur Kolom:

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | INT | Primary Key, Auto Increment |
| `username` | VARCHAR(50) | Username untuk login (UNIQUE) |
| `password` | VARCHAR(255) | Password (dihash dengan password_hash) |
| `nama` | VARCHAR(100) | Nama lengkap user |
| `created_at` | TIMESTAMP | Waktu pembuatan user (auto) |

### Query Create Table:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Data Default:
- **Username:** `admin`
- **Password:** `admin123` (dihash)
- **Nama:** `Administrator`

---

## 2. Tabel `transaksi` (Tabel Transaksi Keuangan)

**Fungsi:** Menyimpan semua data transaksi keuangan (pemasukan & pengeluaran)

### Struktur Kolom:

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | INT | Primary Key, Auto Increment |
| `tanggal` | DATE | Tanggal transaksi |
| `kategori` | VARCHAR(100) | Kategori transaksi (contoh: Service Motor, Beli Sparepart) |
| `keterangan` | TEXT | Keterangan lengkap (backward compatibility) |
| `unit_keterangan` | VARCHAR(255) | Unit/Keterangan (contoh: Motor Honda CBR 150R) |
| `jasa_detail` | TEXT | Jasa dan detail pengerjaan |
| `barang_sparepart` | TEXT | Barang dan harga sparepart |
| `jenis` | ENUM | Jenis transaksi: 'pemasukan' atau 'pengeluaran' |
| `jumlah` | DECIMAL(15,2) | Nominal transaksi |
| `user_id` | INT | Foreign Key ke tabel users |
| `created_at` | TIMESTAMP | Waktu pembuatan (auto) |
| `updated_at` | TIMESTAMP | Waktu update terakhir (auto) |

### Query Create Table:

```sql
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    keterangan TEXT,
    unit_keterangan VARCHAR(255) NULL,
    jasa_detail TEXT NULL,
    barang_sparepart TEXT NULL,
    jenis ENUM('pemasukan', 'pengeluaran') NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Relasi:
- `user_id` → `users.id` (Foreign Key)
- Jika user dihapus, semua transaksinya juga terhapus (ON DELETE CASCADE)

---

## Ringkasan

### Total Tabel: **2 tabel**

1. ✅ **users** - Data user/admin
2. ✅ **transaksi** - Data transaksi keuangan

### Total Kolom di Tabel `transaksi`: **12 kolom**

1. `id` - Primary Key
2. `tanggal` - Tanggal transaksi
3. `kategori` - Kategori transaksi
4. `keterangan` - Keterangan (backward compatibility)
5. `unit_keterangan` - Unit/Keterangan (baru)
6. `jasa_detail` - Jasa & detail pengerjaan (baru)
7. `barang_sparepart` - Barang & sparepart (baru)
8. `jenis` - Jenis (pemasukan/pengeluaran)
9. `jumlah` - Nominal
10. `user_id` - Foreign Key ke users
11. `created_at` - Waktu dibuat
12. `updated_at` - Waktu diupdate

---

## Query untuk Melihat Struktur Database

### Lihat semua tabel:
```sql
SHOW TABLES;
```

### Lihat struktur tabel users:
```sql
DESCRIBE users;
-- atau
SHOW COLUMNS FROM users;
```

### Lihat struktur tabel transaksi:
```sql
DESCRIBE transaksi;
-- atau
SHOW COLUMNS FROM transaksi;
```

### Lihat semua data users:
```sql
SELECT * FROM users;
```

### Lihat semua data transaksi:
```sql
SELECT * FROM transaksi;
```

---

## Catatan Penting

- ✅ Database hanya memiliki **2 tabel** saja
- ✅ Tabel `users` untuk autentikasi
- ✅ Tabel `transaksi` untuk semua data keuangan
- ✅ Relasi: `transaksi.user_id` → `users.id`
- ✅ Kolom baru (`unit_keterangan`, `jasa_detail`, `barang_sparepart`) ditambahkan via ALTER TABLE

---

## Diagram Relasi

```
┌─────────────┐
│    users    │
├─────────────┤
│ id (PK)     │◄──────┐
│ username    │       │
│ password    │       │
│ nama        │       │
│ created_at  │       │
└─────────────┘       │
                      │
                      │ FOREIGN KEY
                      │
┌─────────────────────┐
│     transaksi       │
├─────────────────────┤
│ id (PK)             │
│ tanggal             │
│ kategori            │
│ keterangan          │
│ unit_keterangan     │
│ jasa_detail         │
│ barang_sparepart    │
│ jenis               │
│ jumlah              │
│ user_id (FK) ───────┘
│ created_at          │
│ updated_at          │
└─────────────────────┘
```

---

## Kesimpulan

**Ya, hanya ada 2 tabel:**
1. `users` - untuk user/admin
2. `transaksi` - untuk data keuangan

Struktur ini sudah cukup untuk sistem manajemen keuangan bengkel. Semua fitur (CRUD, laporan, PDF) menggunakan 2 tabel ini.
