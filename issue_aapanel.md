# Issue: Bug Upload Foto & Delete Modal di aaPanel Production

## Ringkasan Masalah

Tiga bug utama yang ditemukan saat deploy di **aaPanel production server**:

1. **Upload foto gagal di server** — di lokal (XAMPP) berhasil, di aaPanel selalu gagal.
2. **Modal konfirmasi hapus tidak muncul** — klik tombol trash tidak memunculkan dialog konfirmasi.
3. **Form Edit Agunan** — data foto & select tidak tampil saat edit.

---

## Bug 1: Upload Foto Gagal di aaPanel

### Gejala
- Form submit berhasil (data tersimpan ke DB) **TAPI** kolom `foto1`–`foto4` tetap `NULL`.
- Di local XAMPP: upload berjalan normal.
- Di aaPanel: `move_uploaded_file()` mengembalikan `false` tanpa pesan error jelas.
- Pesan error di browser: _"Terjadi kesalahan jaringan/server"_ (lihat screenshot).

### Root Cause
Masalah **permission folder** dan **path absolut** di lingkungan aaPanel Linux:

1. Folder `uploads/agunan/` tidak ada atau tidak punya izin **write** untuk user PHP-FPM.
2. Di aaPanel, PHP berjalan sebagai user isolasi (misal `www-data` atau user virtual), berbeda dengan XAMPP yang biasanya `root`.
3. Fungsi `mkdir()` tanpa error checking → folder gagal dibuat secara diam-diam.

### Solusi Kode (sudah diimplementasikan)
Di `api/helpers/upload.php`:
- Tambahkan `error_log()` di setiap titik kegagalan agar mudah di-debug via log.
- Cek `is_writable($targetFolder)` sebelum upload dan kembalikan pesan error yang jelas.
- Gunakan `@mkdir($targetFolder, 0777, true)` dengan error checking eksplisit.

### Solusi Server (aaPanel — WAJIB dilakukan)

#### Langkah 1: Set Permission Folder uploads/
```bash
# Masuk ke terminal aaPanel (menu: Terminal atau SSH)
cd /www/wwwroot/[nama-domain-anda]/

# Buat folder jika belum ada
mkdir -p uploads/agunan

# Set permission agar PHP bisa menulis
chmod -R 755 uploads/
# ATAU jika masih gagal, gunakan 777 (less secure tapi pasti works):
chmod -R 777 uploads/
```

#### Langkah 2: Set Ownership (jika masih gagal)
```bash
# Cek user yang menjalankan PHP-FPM
ps aux | grep php-fpm | head -3

# Set ownership ke user PHP-FPM (ganti 'www-data' dengan user Anda)
chown -R www-data:www-data uploads/

# Atau gunakan user aaPanel
chown -R [your-aapanel-user]:www-data uploads/
```

#### Langkah 3: Verifikasi di aaPanel Panel
1. Buka **aaPanel → Website → [nama site] → File Manager**
2. Navigasi ke folder `uploads/`
3. Klik kanan → **Permissions** → ubah ke `755` atau `777`
4. Centang **"Apply to subdirectories"**

#### Langkah 4: Cek Konfigurasi PHP Upload di aaPanel
1. Buka **aaPanel → App Store → PHP [versi] → Config**
2. Pastikan nilai berikut cukup besar:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 20M
   max_execution_time = 60
   ```
3. Klik **Save** dan **Restart PHP-FPM**.

#### Langkah 5: Cek PHP Error Log
Jika masih gagal, cek error log untuk melihat pesan dari `error_log()`:
```bash
# Path log aaPanel biasanya:
tail -100 /www/wwwlogs/[domain]-error.log
# ATAU
tail -100 /var/log/php-fpm/error.log
```
Cari baris yang mengandung `[UPLOAD ERROR]` atau `[UPLOAD EXCEPTION]`.

---

## Bug 2: Modal Konfirmasi Hapus Tidak Muncul

### Gejala
- Klik tombol trash icon di tabel Calon Cessie → tidak ada modal yang muncul.
- Data juga tidak terhapus.

### Root Cause
**HTML nesting error**: Tag penutup `</div>` untuk `detailModal` hilang di file `calon_cessie.php`, sehingga `deleteModal` secara tidak sengaja **masuk ke dalam struktur DOM `detailModal`**.

Akibatnya:
- `deleteModal` terblokir oleh `detailModal` yang memiliki `display: none`.
- Fungsi JavaScript `openDeleteModal()` mengubah class `deleteModal` ke `flex`, tapi elemen tetap tidak terlihat karena parent-nya (`detailModal`) masih `hidden`.

### Solusi Kode (sudah diimplementasikan)
Di `client/includes/views/calon_cessie.php`:
- Tambahkan `</div>` penutup yang hilang pada baris setelah `detailModal`.
- Pastikan `deleteModal` berada sebagai **sibling** (sejajar), bukan child dari `detailModal`.
- Ubah fungsi `openDeleteModal()` untuk menambahkan class `flex` sekaligus (bukan hanya `remove('hidden')`).

---

## Bug 3: Data Foto & Select Tidak Tampil Saat Edit Agunan

### Gejala
- Buka halaman Edit Agunan (`?id=xxx`) → semua field select (Jenis Surat, Status) kosong.
- Foto yang sudah diupload tidak ditampilkan.
- ID Calon Cessie bisa diubah (harusnya locked).

### Root Cause
Data sudah berhasil ditarik dari DB via PDO, namun elemen `<option>` tidak memiliki atribut `selected` yang di-set secara dinamis, sehingga browser merender semua dropdown dalam kondisi kosong.

Selain itu, `<select disabled>` tidak mengirimkan nilai ke `$_POST`, sehingga `id_calon_cessie` hilang saat update.

### Solusi Kode (sudah diimplementasikan)
Di `client/includes/views/form_agunan.php`:
- Tambahkan `<?= ... ? 'selected' : '' ?>` di setiap `<option>`.
- Tambahkan `<input type="hidden" name="id_calon_cessie" value="...">` agar nilai tetap terkirim walau dropdown disabled.

---

## Checklist Implementasi untuk AI/Developer

- [ ] **Backend** — `api/helpers/upload.php`: Perbaiki error handling & logging ✅
- [ ] **Frontend** — `client/includes/views/calon_cessie.php`: Fix HTML nesting deleteModal ✅
- [ ] **Frontend** — `client/includes/views/form_agunan.php`: Fix selected options & hidden input ✅
- [ ] **Server** — Set permission folder `uploads/` di aaPanel (`chmod -R 755 uploads/`) ⬅ **MANUAL**
- [ ] **Server** — Verifikasi `upload_max_filesize` di PHP settings aaPanel ⬅ **MANUAL**
- [ ] **Server** — `git pull origin main` di terminal aaPanel setelah PR merged ⬅ **MANUAL**

---

## File yang Terdampak

| File | Perubahan |
|------|-----------|
| `api/helpers/upload.php` | Error logging, folder permission check, writable check |
| `client/includes/views/calon_cessie.php` | Fix HTML nesting, fix JS openDeleteModal |
| `client/includes/views/form_agunan.php` | Fix selected options, hidden input id_calon_cessie |
| `api/controllers/CessieController.php` | Hapus agunan terkait sebelum delete master |

---

_Issue ini dibuat otomatis. Jika ada pertanyaan, buka GitHub Discussion._
