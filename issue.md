# Issue: Reload Sangat Lama (Timeout/Hang) Saat Akses Form di aaPanel

## 1. Deskripsi Permasalahan
Saat mengakses halaman Form atau List (seperti `calon_cessie.php`, `form_agunan.php`, dan `form_cessie.php`) di server aaPanel, halaman mengalami *loading* yang sangat lama (bisa puluhan detik) sebelum akhirnya terbuka, atau bahkan gagal memuat sama sekali. 

Anehnya, bug ini **tidak terjadi di Local XAMPP**. 

## 2. Hasil Analisa Akar Masalah (Root Cause)
Setelah dilakukan pengecekan mendalam terhadap *source code*, akar masalahnya berada pada penggunaan **HTTP Request Loopback di sisi Server (Server-side Loopback Request)**.

Pada bagian atas file-file *views* tersebut, Anda menggunakan fungsi PHP berikut:
```php
$apiUrl = API_URL . "/agunan/detail?id=" . $id_edit;
$response = @file_get_contents($apiUrl);

// dan juga..
$urlCessieList = API_URL . "/cessie/list?limit=10000";
$resCessie = @file_get_contents($urlCessieList);
```
Perlu diketahui bahwa `API_URL` di production bernilai `https://lelang.bkkjateng.co.id/api`.
Ketika Nginx/PHP-FPM di aaPanel mengeksekusi script ini, **Server mencoba mengakses dirinya sendiri melalui jaringan internet HTTPS**.

Ini memicu dua kemungkinan besar yang menyebabkan *loading* sangat lama:
### A. Terjadinya PHP-FPM Deadlock & Session Lock
Di aaPanel, PHP berjalan menggunakan *Worker Pool*. Saat Worker A sedang merender halaman `form_agunan.php`, ia tiba-tiba melakukan HTTP Request ke API-nya sendiri. Akibatnya, server harus menyuruh Worker B untuk memproses rute API tersebut.
Masalahnya: Jika aplikasi menggunakan session (`session_start()`), Worker A akan "mengunci" file session pengguna. Saat Worker B dipanggil untuk melayani API (yang mungkin berada di environment yang sama), Worker B tertahan (*waiting/hang*) karena file session masih dikunci oleh Worker A. Keduanya saling tunggu tanpa akhir (*Deadlock*), sampai batas waktu maksimal PHP habis (biasanya 30-60 detik).

### B. SSL Handshake & DNS Loopback Timeout
Fungsi `file_get_contents()` yang menembak domain eksternal dengan `https://` mensyaratkan verifikasi sertifikat SSL. Banyak konfigurasi server Linux (seperti aaPanel) menolak atau kesulitan melakukan koneksi TLS/HTTPS ke alamat IP publiknya sendiri (dikenal sebagai *NAT Hairpinning / Loopback block*). Akibatnya request tertahan sangat lama hingga akhirnya *timeout*.

Di XAMPP Local, hal ini aman-aman saja karena berjalan di jaringan lokal (`http://localhost`) tanpa sertifikat SSL dan tanpa manajemen *worker/process* yang ketat.

## 3. Rencana Solusi (Proposed Fix)

Karena kode API dan Client berada di dalam satu server / repository yang sama, meminta PHP menembak HTTP API-nya sendiri adalah sebuah *anti-pattern* yang fatal. Berikut adalah perbaikannya:

### Migrasi Fetching Data ke Sisi Client (JavaScript)
Ini adalah praktik paling modern dan aman. 
**Tindakan:**
1. Hapus total penggunaan `@file_get_contents()` di semua file `.php` (Form & Views).
2. Biarkan PHP hanya merender tampilan (HTML) form kosong.
3. Gunakan JavaScript `fetch()` di sisi *frontend* (yang berjalan di browser pengguna) untuk memanggil rute API tersebut (misalnya saat `document.ready`).
4. Dengan cara ini, request API dilakukan oleh *browser pengunjung*, **bukan** oleh Server. Sehingga server terbebas dari beban *loopback* dan *deadlock*. Aplikasi akan berjalan secepat kilat!

## Kesimpulan
Bug ini murni diakibatkan oleh *Server-side Loopback HTTP Request* via `file_get_contents()`. Jika setuju dengan rencana solusi di atas, kita dapat segera membuatkan eksekusi perbaikannya.
