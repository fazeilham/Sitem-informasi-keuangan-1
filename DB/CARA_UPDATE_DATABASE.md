# Cara Update Database - Bengkel Biyai Racing Shop

## Metode 1: Menggunakan phpMyAdmin (Paling Mudah) ⭐

### Langkah-langkah:

1. **Buka phpMyAdmin**
   - Buka browser
   - Akses: `http://localhost/phpmyadmin`
   - Login dengan username dan password MySQL Anda

2. **Pilih Database**
   - Di sidebar kiri, klik database: **financetracker_bengkel**
   - Jika belum ada, buat dulu database dengan nama tersebut

3. **Buka Tab SQL**
   - Klik tab **SQL** di menu atas

4. **Copy Paste Query**
   - Copy query dari file `UPDATE_DATABASE_PHPMYADMIN.sql`
   - Atau copy query di bawah ini:

```sql
USE financetracker_bengkel;

ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) NULL AFTER kategori;
ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT NULL AFTER unit_keterangan;
ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT NULL AFTER jasa_detail;
```

5. **Jalankan Query**
   - Klik tombol **Go** atau tekan **Ctrl+Enter**
   - Selesai! ✅

---

## Metode 2: Menggunakan MySQL CLI (Command Line)

### Windows (CMD/PowerShell):

```bash
# Masuk ke MySQL
mysql -u root -p

# Setelah masuk, jalankan:
USE financetracker_bengkel;

ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) NULL AFTER kategori;
ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT NULL AFTER unit_keterangan;
ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT NULL AFTER jasa_detail;

# Keluar dari MySQL
EXIT;
```

### Atau import langsung dari file:

```bash
mysql -u root -p financetracker_bengkel < DB/UPDATE_DATABASE_PHPMYADMIN.sql
```

### Linux/Mac (Terminal):

```bash
# Masuk ke MySQL
mysql -u root -p

# Setelah masuk, jalankan:
USE financetracker_bengkel;

ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) NULL AFTER kategori;
ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT NULL AFTER unit_keterangan;
ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT NULL AFTER jasa_detail;

# Keluar dari MySQL
EXIT;
```

### Atau import langsung dari file:

```bash
mysql -u root -p financetracker_bengkel < DB/UPDATE_DATABASE_PHPMYADMIN.sql
```

---

## Metode 3: Import File SQL

1. **Buka phpMyAdmin**
2. **Pilih database**: financetracker_bengkel
3. **Klik tab Import**
4. **Choose File** → Pilih file `UPDATE_DATABASE_PHPMYADMIN.sql`
5. **Klik Go**
6. **Selesai!** ✅

---

## Query SQL Lengkap (Copy Paste Ini)

```sql
USE financetracker_bengkel;

ALTER TABLE transaksi ADD COLUMN unit_keterangan VARCHAR(255) NULL AFTER kategori;
ALTER TABLE transaksi ADD COLUMN jasa_detail TEXT NULL AFTER unit_keterangan;
ALTER TABLE transaksi ADD COLUMN barang_sparepart TEXT NULL AFTER jasa_detail;
```

---

## Verifikasi Update

Setelah menjalankan query, verifikasi dengan:

```sql
DESCRIBE transaksi;
```

atau

```sql
SHOW COLUMNS FROM transaksi;
```

Pastikan kolom berikut sudah ada:
- ✅ `unit_keterangan`
- ✅ `jasa_detail`
- ✅ `barang_sparepart`

---

## Troubleshooting

### Error: "Table doesn't exist"
**Solusi:** Pastikan database `financetracker_bengkel` sudah dibuat. Jika belum, jalankan `setup.php` terlebih dahulu.

### Error: "Duplicate column name"
**Solusi:** Kolom sudah ada, tidak perlu ditambahkan lagi. Langsung gunakan aplikasi.

### Error: "Access denied"
**Solusi:** 
- Pastikan username dan password MySQL benar
- Pastikan user memiliki hak akses untuk ALTER TABLE

### Error: "Database doesn't exist"
**Solusi:** Buat database terlebih dahulu:
```sql
CREATE DATABASE financetracker_bengkel;
```

---

## Catatan Penting

- ✅ Query ini **AMAN** - tidak akan menghapus data yang sudah ada
- ✅ Jika kolom sudah ada, akan muncul error "Duplicate column name" - ini normal
- ✅ Data yang sudah ada **TIDAK AKAN HILANG**
- ✅ Kolom baru akan otomatis NULL untuk data lama

---

## File SQL yang Tersedia

1. **UPDATE_DATABASE_PHPMYADMIN.sql** - Untuk phpMyAdmin (paling mudah)
2. **UPDATE_DATABASE_CLI.sql** - Untuk MySQL CLI dengan IF NOT EXISTS
3. **update_database_simple.sql** - Versi sederhana

Pilih salah satu yang paling mudah untuk Anda!
