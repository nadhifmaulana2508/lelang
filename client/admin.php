<?php
session_start();

// ==========================================
// 1. PANGGIL KONEKSI DATABASE
// ==========================================
require_once __DIR__ . '/../api/config/database.php';

// ==========================================
// 2. LOGIKA AUTHENTICATION & ROLE
// ==========================================
if (isset($_POST['login'])) {
    $kode_kantor = $_POST['kode_kantor'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Dummy validasi (000 Superadmin, 001-028 Cabang)
    if ($kode_kantor !== '' && $pass === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['kode_kantor'] = $kode_kantor;
        $_SESSION['role'] = ($kode_kantor === '000') ? 'superadmin' : 'cabang';
        header("Location: admin.php");
        exit;
    } else {
        $error_login = "Kode Kantor atau Password salah!";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$is_logged_in = $_SESSION['logged_in'] ?? false;
$user_kode = $_SESSION['kode_kantor'] ?? '';
$is_superadmin = ($_SESSION['role'] ?? '') === 'superadmin';
$page = $_GET['page'] ?? 'dashboard';

// Direktori Upload Gambar
$upload_dir = __DIR__ . '/../img/agunan/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// ==========================================
// 3. LOGIKA PROSES CRUD (CREATE, UPDATE, DELETE)
// ==========================================
if ($is_logged_in) {
    
    // --- PROSES DELETE ASET (KHUSUS 000) ---
    if (isset($_GET['delete']) && $is_superadmin) {
        $id_del = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM dummy_asset WHERE id = ?");
        $stmt->execute([$id_del]);
        header("Location: admin.php?page=data&msg=deleted");
        exit;
    }

    // --- PROSES SIMPAN DATA (CREATE & UPDATE) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_asset'])) {
        $id = $_POST['id'] ?? '';
        $is_update = !empty($id);

        // ✅ BUG FIXED: Tampil Checkbox
        // Jika dicentang bernilai 1, jika tidak dicentang bernilai 0
        $tampil_val = isset($_POST['tampil']) ? 1 : 0;

        // Ambil Data dari Form
        $data = [
            'kode_kantor' => $is_superadmin ? $_POST['kode_kantor'] : $user_kode,
            'rekening' => $_POST['rekening'] ?? '',
            'nasabah' => $_POST['nasabah'] ?? '',
            'alamat_asset' => $_POST['alamat_asset'] ?? '',
            'letak_jaminan' => $_POST['letak_jaminan'] ?? '',
            'jenis_agunan' => $_POST['jenis_agunan'] ?? '',
            'jenis_surat' => $_POST['jenis_surat'] ?? '',
            'nomor_surat' => $_POST['nomor_surat'] ?? '',
            'luas_bangunan' => !empty($_POST['luas_bangunan']) ? $_POST['luas_bangunan'] : 0,
            'luas_tanah' => !empty($_POST['luas_tanah']) ? $_POST['luas_tanah'] : 0,
            'lantai' => !empty($_POST['lantai']) ? $_POST['lantai'] : 0,
            'harga_jual' => !empty($_POST['harga_jual']) ? str_replace('.', '', $_POST['harga_jual']) : 0,
            'proses_penjualan' => $_POST['proses_penjualan'] ?? 'Jual',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'link_maps' => $_POST['link_maps'] ?? '',
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
            'tampil' => $tampil_val 
        ];

        // Proses Upload 4 Foto
        $foto_fields = ['foto1', 'foto2', 'foto3', 'foto4'];
        foreach ($foto_fields as $f) {
            $data[$f] = $_POST['old_' . $f] ?? ''; // Simpan foto lama sbg default
            if (isset($_FILES[$f]) && $_FILES[$f]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION);
                $new_filename = time() . '_' . rand(100, 999) . '_' . $f . '.' . $ext;
                if (move_uploaded_file($_FILES[$f]['tmp_name'], $upload_dir . $new_filename)) {
                    $data[$f] = $new_filename;
                }
            }
        }

        if ($is_update) {
            // Logika Update (Role Cabang hanya bisa update jika kode_kantor miliknya)
            $sql = "UPDATE dummy_asset SET kode_kantor=:kode_kantor, rekening=:rekening, nasabah=:nasabah, alamat_asset=:alamat_asset, letak_jaminan=:letak_jaminan, jenis_agunan=:jenis_agunan, jenis_surat=:jenis_surat, nomor_surat=:nomor_surat, luas_bangunan=:luas_bangunan, luas_tanah=:luas_tanah, lantai=:lantai, harga_jual=:harga_jual, proses_penjualan=:proses_penjualan, deskripsi=:deskripsi, link_maps=:link_maps, latitude=:latitude, longitude=:longitude, tampil=:tampil, foto1=:foto1, foto2=:foto2, foto3=:foto3, foto4=:foto4 WHERE id=:id";
            if(!$is_superadmin) $sql .= " AND kode_kantor='$user_kode'";
            
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: admin.php?page=data&msg=updated");
        } else {
            // Logika Create
            $sql = "INSERT INTO dummy_asset (kode_kantor, rekening, nasabah, alamat_asset, letak_jaminan, jenis_agunan, jenis_surat, nomor_surat, luas_bangunan, luas_tanah, lantai, harga_jual, proses_penjualan, deskripsi, link_maps, latitude, longitude, tampil, foto1, foto2, foto3, foto4) VALUES (:kode_kantor, :rekening, :nasabah, :alamat_asset, :letak_jaminan, :jenis_agunan, :jenis_surat, :nomor_surat, :luas_bangunan, :luas_tanah, :lantai, :harga_jual, :proses_penjualan, :deskripsi, :link_maps, :latitude, :longitude, :tampil, :foto1, :foto2, :foto3, :foto4)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: admin.php?page=data&msg=created");
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BKK Jateng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="text-gray-800">

<?php if (!$is_logged_in): ?>
    <div class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-blue-900 to-blue-700">
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-2xl w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl"><i class="fas fa-shield-alt"></i></div>
                <h2 class="text-2xl font-extrabold text-gray-900">Login Admin</h2>
                <p class="text-gray-500 text-sm mt-2">Masuk menggunakan Kode Kantor</p>
            </div>
            <?php if(isset($error_login)): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm font-bold text-center mb-4"><i class="fas fa-exclamation-circle mr-1"></i> <?= $error_login ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode Kantor</label>
                    <input type="text" name="kode_kantor" placeholder="Contoh: 000 atau 001" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" name="login" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 shadow-lg">Masuk <i class="fas fa-sign-in-alt ml-2"></i></button>
            </form>
        </div>
    </div>

<?php else: ?>
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-white border-r border-gray-200 flex-col hidden md:flex z-20">
            <div class="h-20 flex items-center px-6 border-b border-gray-100">
                <h1 class="text-xl font-extrabold text-blue-900"><i class="fas fa-database text-blue-600 mr-2"></i> Panel Aset</h1>
            </div>
            <div class="p-4">
                <div class="bg-blue-50 rounded-xl p-4 mb-6 border border-blue-100">
                    <p class="text-xs text-blue-500 font-bold uppercase mb-1">Login Sebagai</p>
                    <p class="font-extrabold text-blue-900 text-lg"><?= $is_superadmin ? 'Superadmin (Pusat)' : 'Cabang ' . $user_kode ?></p>
                </div>
                <nav class="space-y-2">
                    <a href="?page=dashboard" class="flex items-center px-4 py-3 <?= $page === 'dashboard' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' ?> rounded-xl font-bold transition-all"><i class="fas fa-chart-pie w-5 mr-3"></i> Dashboard</a>
                    <a href="?page=data" class="flex items-center px-4 py-3 <?= in_array($page, ['data', 'form', 'view']) ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' ?> rounded-xl font-bold transition-all"><i class="fas fa-folder-open w-5 mr-3"></i> Data Aset</a>
                    
                    <a href="?page=pengajuan" class="flex items-center px-4 py-3 <?= $page === 'pengajuan' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' ?> rounded-xl font-bold transition-all"><i class="fas fa-envelope-open-text w-5 mr-3"></i> Data Pengajuan</a>
                </nav>
            </div>
            <div class="mt-auto p-4 border-t border-gray-100">
                <a href="?action=logout" class="flex items-center justify-center px-4 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold transition-all"><i class="fas fa-sign-out-alt mr-2"></i> Keluar</a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50">
            <header class="h-16 md:h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 z-10">
                <div class="md:hidden flex items-center"><h1 class="text-lg font-extrabold text-blue-900"><i class="fas fa-database text-blue-600"></i> Admin</h1></div>
                <div class="md:hidden flex gap-2">
                    <a href="?page=dashboard" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-bold text-gray-700"><i class="fas fa-chart-pie"></i></a>
                    <a href="?page=data" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-bold text-gray-700"><i class="fas fa-folder-open"></i></a>
                    <a href="?page=pengajuan" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-bold text-gray-700"><i class="fas fa-envelope-open-text"></i></a>
                    <a href="?action=logout" class="px-3 py-2 bg-red-100 rounded-lg text-sm font-bold text-red-600"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-8">
                
                <?php if ($page === 'dashboard'): 
                    $whereD = $is_superadmin ? "1=1" : "kode_kantor = '$user_kode'";
                    $tot = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD")->fetchColumn();
                    $tJual = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD AND proses_penjualan='Jual'")->fetchColumn();
                    $tLel = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD AND proses_penjualan='Lelang'")->fetchColumn();
                    $tCes = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD AND proses_penjualan='Cessie'")->fetchColumn();
                ?>
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Dashboard Rekap</h2>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100"><p class="text-gray-500 text-xs font-bold uppercase mb-1">Total Aset</p><h3 class="text-3xl font-black text-blue-600"><?= $tot ?></h3></div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100"><p class="text-gray-500 text-xs font-bold uppercase mb-1">Jual Damai</p><h3 class="text-3xl font-black text-gray-800"><?= $tJual ?></h3></div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100"><p class="text-gray-500 text-xs font-bold uppercase mb-1">Lelang</p><h3 class="text-3xl font-black text-gray-800"><?= $tLel ?></h3></div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100"><p class="text-gray-500 text-xs font-bold uppercase mb-1">Cessie</p><h3 class="text-3xl font-black text-gray-800"><?= $tCes ?></h3></div>
                    </div>
                <?php endif; ?>

                <?php if ($page === 'pengajuan'): 
                    // Filter Cabang (Superadmin)
                    $filter_cabang = $_GET['filter_cabang'] ?? '';
                    // Filter Skema
                    $filter_skema = $_GET['filter_skema'] ?? '';

                    // Logika Join & Where
                    $whereP = $is_superadmin ? "1=1" : "da.kode_kantor = '$user_kode'";
                    if ($is_superadmin && $filter_cabang !== '') $whereP .= " AND da.kode_kantor = '$filter_cabang'";
                    if ($filter_skema !== '') $whereP .= " AND pa.minat_skema = '$filter_skema'";

                    // Pagination
                    $limit = 10;
                    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                    $offset = ($p - 1) * $limit;
                    
                    // Total Data
                    $totData = $pdo->query("SELECT COUNT(*) FROM pengajuan_aset pa JOIN dummy_asset da ON pa.id_aset = da.id WHERE $whereP")->fetchColumn();
                    $totPage = ceil($totData / $limit);

                    // Fetch Data
                    $stmtP = $pdo->query("
                        SELECT pa.*, da.jenis_surat, da.nomor_surat, da.kode_kantor 
                        FROM pengajuan_aset pa 
                        JOIN dummy_asset da ON pa.id_aset = da.id 
                        WHERE $whereP 
                        ORDER BY pa.id DESC 
                        LIMIT $limit OFFSET $offset
                    ");
                    $pengajuans = $stmtP->fetchAll(PDO::FETCH_ASSOC);
                ?>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold text-gray-900">Data Pengajuan Masuk</h2>
                            <p class="text-gray-500 text-sm mt-1">Daftar form peminatan dari website katalog.</p>
                        </div>
                        
                        <div class="flex flex-wrap gap-3 w-full md:w-auto">
                            <form method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
                                <input type="hidden" name="page" value="pengajuan">
                                
                                <?php if($is_superadmin): 
                                    $cabangs = $pdo->query("SELECT DISTINCT kode_kantor FROM dummy_asset ORDER BY kode_kantor")->fetchAll(PDO::FETCH_COLUMN);
                                ?>
                                <select name="filter_cabang" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold outline-none">
                                    <option value="">Semua Cabang</option>
                                    <?php foreach($cabangs as $c): ?>
                                        <option value="<?= $c ?>" <?= $filter_cabang==$c?'selected':'' ?>>Cabang <?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>

                                <select name="filter_skema" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold outline-none">
                                    <option value="">Semua Skema</option>
                                    <option value="Pembiayaan BKK Joglo (KPR)" <?= $filter_skema=='Pembiayaan BKK Joglo (KPR)'?'selected':'' ?>>BKK Joglo (KPR)</option>
                                    <option value="Mekanisme Lelang" <?= $filter_skema=='Mekanisme Lelang'?'selected':'' ?>>Lelang</option>
                                    <option value="Mekanisme Cessie" <?= $filter_skema=='Mekanisme Cessie'?'selected':'' ?>>Cessie</option>
                                    <option value="Hanya Tanya-tanya Dulu" <?= $filter_skema=='Hanya Tanya-tanya Dulu'?'selected':'' ?>>Tanya-tanya</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-bold">
                                    <tr>
                                        <th class="p-4">Tanggal</th>
                                        <th class="p-4">Pengirim</th>
                                        <th class="p-4">Aset Dituju</th>
                                        <th class="p-4">Skema</th>
                                        <?php if($is_superadmin): ?><th class="p-4">Cabang</th><?php endif; ?>
                                        <th class="p-4 text-center">Aksi WA</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    <?php if(count($pengajuans) > 0): ?>
                                        <?php foreach($pengajuans as $row): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-4 text-gray-600"><?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')) ?></td>
                                            <td class="p-4">
                                                <p class="font-bold text-gray-800"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></p>
                                                <p class="text-xs text-blue-600 font-bold"><?= htmlspecialchars($row['no_wa'] ?? '-') ?></p>
                                            </td>
                                            <td class="p-4 font-bold text-gray-800"><?= $row['jenis_surat'] ?> - <?= $row['nomor_surat'] ?></td>
                                            <td class="p-4"><span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded text-xs font-bold"><?= htmlspecialchars($row['minat_skema'] ?? '-') ?></span></td>
                                            <?php if($is_superadmin): ?><td class="p-4 font-bold text-gray-500"><?= $row['kode_kantor'] ?></td><?php endif; ?>
                                            <td class="p-4 text-center">
                                                <?php
                                                    $pesanBalasan = "Halo " . ($row['nama_lengkap'] ?? '') . ", kami dari BKK Jateng menerima pengajuan Anda untuk aset " . $row['jenis_surat'] . " " . $row['nomor_surat'] . " dengan skema " . ($row['minat_skema'] ?? '') . ". Ada yang bisa kami bantu lebih lanjut?";
                                                    $linkWa = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_wa'] ?? '') . "?text=" . urlencode($pesanBalasan);
                                                ?>
                                                <a href="<?= $linkWa ?>" target="_blank" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow-sm">
                                                    <i class="fab fa-whatsapp mr-1.5"></i> Hubungi
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada pengajuan masuk.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 border-t border-gray-100 flex justify-center gap-2">
                            <?php for($i=1; $i<=$totPage; $i++): ?>
                                <a href="?page=pengajuan&p=<?= $i ?>&filter_cabang=<?= $filter_cabang ?>&filter_skema=<?= $filter_skema ?>" class="w-8 h-8 flex items-center justify-center rounded-lg font-bold <?= $i==$p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($page === 'data'): 
                    // FILTER CABANG (Hanya Superadmin)
                    $filter_cabang = $_GET['filter_cabang'] ?? '';
                    $whereD = $is_superadmin ? "1=1" : "kode_kantor = '$user_kode'";
                    if ($is_superadmin && $filter_cabang !== '') $whereD .= " AND kode_kantor = '$filter_cabang'";

                    // PAGINATION
                    $limit = 10;
                    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                    $offset = ($p - 1) * $limit;
                    $totData = $pdo->query("SELECT COUNT(*) FROM dummy_asset WHERE $whereD")->fetchColumn();
                    $totPage = ceil($totData / $limit);

                    $stmt = $pdo->query("SELECT * FROM dummy_asset WHERE $whereD ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <h2 class="text-2xl font-extrabold text-gray-900">Kelola Data Aset</h2>
                        
                        <div class="flex gap-3 w-full md:w-auto">
                            <?php if($is_superadmin): 
                                $cabangs = $pdo->query("SELECT DISTINCT kode_kantor FROM dummy_asset ORDER BY kode_kantor")->fetchAll(PDO::FETCH_COLUMN);
                            ?>
                            <form method="GET" class="flex gap-2 w-full">
                                <input type="hidden" name="page" value="data">
                                <select name="filter_cabang" onchange="this.form.submit()" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm font-bold focus:ring-blue-500 outline-none">
                                    <option value="">Semua Cabang</option>
                                    <?php foreach($cabangs as $c): ?>
                                        <option value="<?= $c ?>" <?= $filter_cabang==$c?'selected':'' ?>>Cabang <?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <?php endif; ?>

                            <a href="?page=form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl flex items-center justify-center shrink-0 shadow-md">
                                <i class="fas fa-plus mr-2"></i> Tambah Aset
                            </a>
                        </div>
                    </div>

                    <?php if(isset($_GET['msg'])): ?>
                        <div class="bg-green-50 text-green-700 p-3 rounded-xl border border-green-200 font-bold mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> Operasi berhasil dilakukan!
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-bold">
                                    <tr>
                                        <th class="p-4">Tampil</th>
                                        <th class="p-4">No. Surat / Nasabah</th>
                                        <th class="p-4">Harga</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4 text-center">Dilihat</th>
                                        <?php if($is_superadmin): ?><th class="p-4">Cabang</th><?php endif; ?>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    <?php foreach($assets as $row): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4">
                                            <?= $row['tampil']==1 ? '<i class="fas fa-eye text-green-500 tooltip" title="Tampil di Web"></i>' : '<i class="fas fa-eye-slash text-red-500 tooltip" title="Disembunyikan"></i>' ?>
                                        </td>
                                        <td class="p-4">
                                            <p class="font-bold text-gray-800"><?= $row['jenis_surat'] ?> - <?= $row['nomor_surat'] ?></p>
                                            <p class="text-xs text-gray-500"><?= $row['nasabah'] ?></p>
                                        </td>
                                        <td class="p-4 font-bold text-blue-600">Rp <?= number_format($row['harga_jual'],0,',','.') ?></td>
                                        <td class="p-4"><span class="bg-gray-100 px-3 py-1 rounded-md text-[10px] font-bold uppercase"><?= $row['proses_penjualan'] ?></span></td>
                                        
                                        <td class="p-4 text-center font-bold text-gray-600">
                                            <i class="fas fa-eye text-blue-400 mr-1"></i> <?= $row['view_count'] ?? 0 ?>
                                        </td>
                                        
                                        <?php if($is_superadmin): ?><td class="p-4 font-bold text-gray-500"><?= $row['kode_kantor'] ?></td><?php endif; ?>
                                        <td class="p-4 text-center flex justify-center gap-2">
                                            <a href="?page=view&id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white"><i class="fas fa-search"></i></a>
                                            <a href="?page=form&id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white"><i class="fas fa-pen"></i></a>
                                            <?php if($is_superadmin): ?>
                                            <a href="?page=data&delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?');" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white"><i class="fas fa-trash"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(count($assets) == 0): ?>
                                        <tr><td colspan="7" class="p-8 text-center text-gray-500">Belum ada data aset jaminan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 border-t border-gray-100 flex justify-center gap-2">
                            <?php for($i=1; $i<=$totPage; $i++): ?>
                                <a href="?page=data&p=<?= $i ?>&filter_cabang=<?= $filter_cabang ?>" class="w-8 h-8 flex items-center justify-center rounded-lg font-bold <?= $i==$p ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($page === 'form'): 
                    $id_edit = $_GET['id'] ?? null;
                    $r = [];
                    if($id_edit) {
                        $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id=?");
                        $stmt->execute([$id_edit]);
                        $r = $stmt->fetch(PDO::FETCH_ASSOC);
                        if(!$is_superadmin && $r['kode_kantor'] !== $user_kode) die("Akses Ditolak.");
                    }
                ?>
                    <div class="flex items-center gap-4 mb-6">
                        <a href="?page=data" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
                        <h2 class="text-2xl font-extrabold text-gray-900"><?= $id_edit ? 'Edit Data Aset' : 'Tambah Aset Baru' ?></h2>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                        <input type="hidden" name="id" value="<?= $id_edit ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-4">
                                <h3 class="font-bold text-blue-600 border-b pb-2">1. Identitas Nasabah</h3>
                                <?php if($is_superadmin): ?>
                                    <div><label class="text-xs font-bold text-gray-500">Kode Kantor</label>
                                    <input type="text" name="kode_kantor" value="<?= $r['kode_kantor'] ?? '' ?>" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
                                <?php endif; ?>
                                <div><label class="text-xs font-bold text-gray-500">Nama Nasabah</label>
                                <input type="text" name="nasabah" value="<?= $r['nasabah'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
                                <div><label class="text-xs font-bold text-gray-500">Nomor Rekening</label>
                                <input type="text" name="rekening" value="<?= $r['rekening'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="font-bold text-blue-600 border-b pb-2">2. Spesifikasi Dokumen</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500">Jenis Agunan</label>
                                    <input type="text" name="jenis_agunan" value="<?= $r['jenis_agunan'] ?? 'Tanah Bangunan' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                    <div><label class="text-xs font-bold text-gray-500">Status Aset</label>
                                    <select name="proses_penjualan" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none">
                                        <option value="Jual" <?= ($r['proses_penjualan']??'')=='Jual'?'selected':'' ?>>Jual Damai</option>
                                        <option value="Lelang" <?= ($r['proses_penjualan']??'')=='Lelang'?'selected':'' ?>>Lelang</option>
                                        <option value="Cessie" <?= ($r['proses_penjualan']??'')=='Cessie'?'selected':'' ?>>Cessie</option>
                                        <option value="Sold" <?= ($r['proses_penjualan']??'')=='Sold'?'selected':'' ?>>Sold</option>
                                    </select></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500">Jenis Surat</label>
                                    <input type="text" name="jenis_surat" value="<?= $r['jenis_surat'] ?? 'SHM' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                    <div><label class="text-xs font-bold text-gray-500">Nomor Surat</label>
                                    <input type="text" name="nomor_surat" value="<?= $r['nomor_surat'] ?? '' ?>" required class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500">Luas Tanah</label>
                                    <input type="number" name="luas_tanah" value="<?= $r['luas_tanah'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                    <div><label class="text-xs font-bold text-gray-500">L. Bangunan</label>
                                    <input type="number" name="luas_bangunan" value="<?= $r['luas_bangunan'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                    <div><label class="text-xs font-bold text-gray-500">Lantai</label>
                                    <input type="number" name="lantai" value="<?= $r['lantai'] ?? '1' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"></div>
                                </div>
                                <div><label class="text-xs font-bold text-gray-500">Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" value="<?= $r['harga_jual'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg font-bold text-blue-600 outline-none"></div>
                            </div>
                        </div>

                        <div class="mb-6 space-y-4">
                            <h3 class="font-bold text-blue-600 border-b pb-2">3. Lokasi & Deskripsi</h3>
                            <div><label class="text-xs font-bold text-gray-500">Alamat Lengkap Aset</label>
                            <textarea name="alamat_asset" rows="2" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"><?= $r['alamat_asset'] ?? '' ?></textarea></div>
                            <div><label class="text-xs font-bold text-gray-500">Deskripsi Menarik</label>
                            <textarea name="deskripsi" rows="3" class="w-full mt-1 px-3 py-2 border rounded-lg outline-none"><?= $r['deskripsi'] ?? '' ?></textarea></div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="text-xs font-bold text-gray-500">Letak Jaminan</label><input type="text" name="letak_jaminan" value="<?= $r['letak_jaminan'] ?? 'Marketable' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
                                <div><label class="text-xs font-bold text-gray-500">Latitude Maps</label><input type="text" name="latitude" value="<?= $r['latitude'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
                                <div><label class="text-xs font-bold text-gray-500">Longitude Maps</label><input type="text" name="longitude" value="<?= $r['longitude'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
                            </div>
                            <div><label class="text-xs font-bold text-gray-500">Link Google Maps</label><input type="text" name="link_maps" value="<?= $r['link_maps'] ?? '' ?>" class="w-full mt-1 px-3 py-2 border rounded-lg"></div>
                        </div>

                        <div class="mb-6">
                            <h3 class="font-bold text-blue-600 border-b pb-2 mb-4">4. Upload Foto Aset</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <?php for($i=1; $i<=4; $i++): $foto_name = $r["foto$i"] ?? ''; ?>
                                    <div class="border rounded-xl p-3 bg-gray-50 text-center">
                                        <p class="text-xs font-bold text-gray-500 mb-2">Foto <?= $i ?></p>
                                        <?php if($foto_name): ?>
                                            <img src="../img/agunan/<?= $foto_name ?>" class="w-full h-24 object-cover rounded-lg mb-2 shadow-sm">
                                            <input type="hidden" name="old_foto<?= $i ?>" value="<?= $foto_name ?>">
                                        <?php else: ?>
                                            <div class="w-full h-24 bg-gray-200 rounded-lg mb-2 flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <input type="file" name="foto<?= $i ?>" accept="image/*" class="text-[10px] w-full file:bg-blue-50 file:text-blue-600 file:border-0 file:rounded-md file:px-2 file:py-1 file:font-bold">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mb-6 bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                            <input type="checkbox" name="tampil" value="1" <?= (!isset($r['tampil']) || $r['tampil']==1) ? 'checked' : '' ?> class="w-5 h-5 text-blue-600 rounded">
                            <label class="text-sm font-bold text-gray-800">Tampilkan aset ini di Katalog Web (Public)</label>
                        </div>

                        <button type="submit" name="save_asset" class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Simpan Data Aset
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($page === 'view'): 
                    $id_view = $_GET['id'];
                    $stmt = $pdo->prepare("SELECT * FROM dummy_asset WHERE id=?");
                    $stmt->execute([$id_view]);
                    $r = $stmt->fetch(PDO::FETCH_ASSOC);
                    if(!$is_superadmin && $r['kode_kantor'] !== $user_kode) die("Akses Ditolak.");
                ?>
                    <div class="flex items-center gap-4 mb-6">
                        <a href="?page=data" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-200 hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
                        <h2 class="text-2xl font-extrabold text-gray-900">Detail Aset #<?= $r['id'] ?></h2>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <?php for($i=1; $i<=4; $i++): if(!empty($r["foto$i"])): ?>
                                <img src="../img/agunan/<?= $r["foto$i"] ?>" class="w-full h-32 md:h-48 object-cover rounded-xl shadow-sm">
                            <?php endif; endfor; ?>
                        </div>
                        
                        <h3 class="text-3xl font-black text-blue-600 mb-1">Rp <?= number_format($r['harga_jual'],0,',','.') ?></h3>
                        <p class="text-xl font-bold text-gray-800 mb-4"><?= $r['jenis_surat'] ?> - <?= $r['nomor_surat'] ?></p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Pemilik/Nasabah:</span> <?= $r['nasabah'] ?> (<?= $r['rekening'] ?>)</p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Cabang Asal:</span> <?= $r['kode_kantor'] ?></p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Status Web:</span> <?= $r['tampil']==1?'Tampil Publik':'Disembunyikan' ?></p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Dilihat:</span> <?= $r['view_count'] ?? 0 ?> kali 👀</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Luas Tanah:</span> <?= $r['luas_tanah'] ?> m²</p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">L. Bangunan:</span> <?= $r['luas_bangunan'] ?> m² (<?= $r['lantai'] ?> Lantai)</p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Alamat:</span> <?= $r['alamat_asset'] ?></p>
                                <p><span class="font-bold text-gray-500 w-32 inline-block">Maps:</span> <a href="<?= $r['link_maps'] ?>" target="_blank" class="text-blue-500 underline">Buka Peta</a></p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl text-sm">
                            <p class="font-bold text-blue-800 mb-1">Deskripsi Tambahan:</p>
                            <p class="text-gray-700"><?= nl2br($r['deskripsi']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
<?php endif; ?>

</body>
</html>