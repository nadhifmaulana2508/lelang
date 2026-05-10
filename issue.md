# Issue: Bug Error Upload Foto (WebP) di Server aaPanel (Kesalahan Jaringan 500)

## 1. Deskripsi Permasalahan
Saat pengguna mencoba men-submit data Agunan beserta foto, muncul peringatan JavaScript bertuliskan: **"Terjadi kesalahan jaringan/server."** Proses simpan pun langsung terhenti. 
Berdasarkan keterangan, masalah ini terjadi di server *production* (aaPanel), sedangkan di *local* (XAMPP) fitur upload foto berfungsi normal dengan membuat file ekstensi `.webp`.

## 2. Analisa Akar Masalah (Root Cause)
Pesan "kesalahan jaringan/server" muncul karena proses `fetch()` JavaScript di *client* tidak menerima balasan JSON dari API, melainkan menerima HTTP Status 500 (Internal Server Error) atau koneksi terputus.

Artinya, **Skrip Backend PHP (API) mengalami Fatal Error (Crash)** saat sedang memproses gambar. 

Di dalam file `api/helpers/upload.php`, terdapat fungsi pemrosesan konversi gambar:
```php
$image = imagecreatefromjpeg($fileInfo["tmp_name"]);
$success = imagewebp($image, $targetFile, 80);
```

**Kenapa Crash di aaPanel?**
Sangat besar kemungkinannya ekstensi **GD Library** pada PHP di aaPanel Anda **tidak dikompilasi dengan dukungan format WebP**. 
- Saat PHP mencoba memanggil fungsi `imagewebp()`, fungsi tersebut dianggap tidak ada (*Undefined Function*) sehingga langsung menyebabkan *Fatal Error* / *Crash*.
- Di XAMPP, ekstensi GD PHP bawaannya sudah dikompilasi secara lengkap beserta dukungan WebP, sehingga proses `imagewebp()` berjalan normal.

*(Catatan: Kemungkinan kecil lain adalah `folder permissions` pada direktori `uploads/` yang tidak bisa ditulis (writable) oleh user Nginx/Apache. Namun ini biasanya hanya memicu Warning, bukan Fatal Error 500, kecuali strict mode nyala).*

## 3. Rencana Solusi (Proposed Fix)

Ada dua opsi untuk menyelesaikan ini. Salah satunya berfokus di Server, dan yang satu lagi berfokus pada Kode (sebagai pengaman).

### A. Solusi Konfigurasi Server (Di Panel aaPanel Anda)
1. Buka dashboard **aaPanel** Anda.
2. Masuk ke menu **App Store** -> Tab **Installed**.
3. Cari versi **PHP** yang sedang aktif (misalnya PHP 8.1), lalu klik **Setting**.
4. Masuk ke bagian **Install extensions**. Pastikan ekstensi **GD**, **fileinfo**, dan **exif** sudah terinstal.
5. *(Jika masih error)* Biasanya beberapa instalasi PHP standar aaPanel belum mencakup flag `--with-webp`. Anda mungkin harus meng-*uninstall* PHP tersebut dan meng-*install* ulangnya menggunakan mode **Compiled (Kompilasi source)** agar WebP Support aktif otomatis di GD.

### B. Solusi Pengaman pada Kode (Graceful Fallback)
Agar aplikasi tidak langsung mati (Crash) jika server tidak mendukung WebP, kita bisa mengubah kode di `api/helpers/upload.php` menjadi lebih pintar:
1. Kode akan mengecek apakah server punya fungsi `imagewebp` dengan `function_exists('imagewebp')`.
2. Jika ada, maka gambar akan dikompres dan dikonversi ke `.webp`.
3. **Jika tidak ada**, maka gambar tidak perlu dikonversi ke WebP, melainkan langsung di-upload apa adanya menggunakan format asli (seperti `.jpg` atau `.png`) dengan fungsi `move_uploaded_file()`.

Dengan *solusi kode* ini, fitur upload akan tetap berjalan dan anti-*crash* apapun server yang digunakan!
