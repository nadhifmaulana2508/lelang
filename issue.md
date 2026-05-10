# Issue: Kumpulan Bug Minor (Agunan, Upload WebP, Delete Modal)

Dokumen ini melacak tiga bug yang ditemukan pada iterasi pengembangan terakhir:

## 1. Bug Form Agunan: ID Tidak Terkunci (Gagal Fetch Data)
- **File**: `client/includes/views/form_agunan.php`
- **Gejala**: Saat mode Edit (`?id=...`), form tampil kosong dan data lama tidak ditarik dari database. 
- **Penyebab**: Terjadi kesalahan ketik (typo) pada *query database*. Sistem mencari data ke tabel `dummy_asset` yang mana seharusnya adalah `cessie_agunan`. Karena data tidak ditemukan, form merender dalam mode "Tambah Baru" (kosong).
- **Solusi**: Memperbaiki nama tabel di dalam pemanggilan PDO menjadi `cessie_agunan`.

## 2. Bug Upload Foto WebP: Fallback Kurang Handal
- **File**: `api/helpers/upload.php`
- **Gejala**: Ketika upload gambar dengan format asli (`.jpg`/`.png`), terkadang masih gagal atau terjadi error di sisi server.
- **Penyebab**: Pengecekan `function_exists('imagewebp')` tidak sepenuhnya menjamin konversi WebP berhasil (misal: memori PHP penuh, atau fungsi `imagecreatefromjpeg` yang justru tidak aktif). Jika konversi gagal di tengah jalan, script tidak otomatis beralih (*fallback*) ke `move_uploaded_file`.
- **Solusi**: Menggunakan struktur `try...catch`. Jika terjadi *Throwable exception* atau kegagalan apapun selama proses konversi `imagewebp`, sistem akan langsung melempar gambar tersebut ke fungsi `fallbackUpload()` (simpan menggunakan format dan file aslinya secara aman).

## 3. Bug UX Delete Cessie: Alert Konvensional
- **File**: `client/includes/views/calon_cessie.php`
- **Gejala**: Saat klik ikon hapus tong sampah, browser memunculkan popup konfirmasi bawaan `confirm()` yang terlihat tidak profesional.
- **Penyebab**: Konfirmasi hapus masih menggunakan JavaScript native `confirm()` dan `alert()`.
- **Solusi**: Mengganti fungsi `deleteCessie()` menjadi sistem *Custom HTML Modal* (seperti `detailModal`) agar tampilan konfirmasi hapus terlihat lebih elegan, lalu otomatis melakukan `fetchData()` ulang agar tabel langsung ter-update (data hilang) tanpa perlu memuat ulang seluruh halaman.

---
Ketiga perbaikan di atas akan segera diimplementasikan dan digabungkan.
