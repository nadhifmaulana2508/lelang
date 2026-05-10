# Issue: Routing Kacau pada Client (Admin) & Warning BASE_URL

## 1. Deskripsi Permasalahan

Setelah penerapan *Centralized Routing* di ROOT `index.php`, seluruh request (termasuk yang berada di subfolder seperti `/client/calon_cessie` atau `/client/form`) berhasil ditangani secara terpusat. Namun, hal ini mengekspos dua kelemahan fatal pada kode lama di dalam folder `client`:

### A. Bug "Constant BASE_URL already defined"
Konstanta `BASE_URL` didefinisikan secara dinamis di `ROOT index.php` untuk memfasilitasi Centralized Routing. Akan tetapi, saat `client/index.php` di-*require* oleh ROOT, ia memanggil `api/config/config.php` yang ternyata **mendefinisikan ulang** konstanta `BASE_URL`. Hal ini memicu *Warning* PHP di aaPanel:
> `Warning: Constant BASE_URL already defined in C:\...\api\config\config.php on line 7`

### B. Bug Routing Form (Relative URL Terjebak)
Di dalam folder `client/includes/views/` (seperti pada `form_aset.php`), link untuk kembali ke halaman data ditulis menggunakan **Relative URL**:
```html
<a href="?page=data">Kembali</a>
```
Karena URL di browser saat itu adalah `lelang.bkkjateng.co.id/client/form`, mengklik link tersebut akan mengubah URL menjadi:
`lelang.bkkjateng.co.id/client/form?page=data`

Router terpusat membaca `$urlParts[1]` sebagai `form`, sehingga parameter `?page=data` diabaikan, dan halaman **tidak mau berpindah** (routing kacau/terjebak di halaman yang sama).

## 2. Rencana Perbaikan (Major Rework)

Untuk memperbaiki sistem routing di admin secara permanen dan profesional, kita perlu merombak seluruh penggunaan routing di `client` menjadi **Absolute URL**:

1. **Perbaikan `api/config/config.php`:**
   Membungkus deklarasi dengan kondisional `if (!defined('BASE_URL')) { ... }` agar tidak terjadi bentrok.
   
2. **Refactoring Sidebar & Components:**
   Mengubah semua menu di `client/includes/components.php`:
   - `href="dashboard"` menjadi `href="<?= BASE_URL ?>/client/dashboard"`
   - `href="?action=logout"` menjadi `href="<?= BASE_URL ?>/client/dashboard?action=logout"`

3. **Refactoring Views & Forms (`client/includes/views/*.php`):**
   Mengganti semua aksi seperti edit, hapus, kembali, dan pagination:
   - `<a href="?page=data">` ➡️ `<a href="<?= BASE_URL ?>/client/data">`
   - `<a href="?page=form&id=...">` ➡️ `<a href="<?= BASE_URL ?>/client/form?id=...">`

4. **Refactoring Logic Redirect (`asset_logic.php` & `auth_logic.php`):**
   Mengubah `header("Location: index.php?page=data")` menjadi `header("Location: " . BASE_URL . "/client/data")`.

## 3. Hasil yang Diharapkan
- Tidak ada lagi *warning constant* di server aaPanel.
- Seluruh form (tambah, edit, delete) dan navigasi di dalam Admin (Client) berjalan 100% mulus baik di Local XAMPP maupun Production Nginx tanpa konflik.
