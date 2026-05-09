<?php
// ==========================================
// 1. LOAD CONFIG UTAMA
// (Kita hapus fungsi BASE_URL di sini karena sudah ada di config.php!)
// ==========================================
require_once __DIR__ . '/../api/config/config.php';

// ==========================================
// 2. CORE ROUTING LOGIC (CLEAN URL)
// (Kita taruh di atas sebelum auth_logic agar sidebar tau kita di page mana)
// ==========================================

// Tarik parameter 'url' dari Nginx/Apache (contoh: url=form_agunan/12)
$url = isset($_GET['url']) && !empty($_GET['url']) ? rtrim($_GET['url'], '/') : 'dashboard';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Pecah Segment 1 (Halaman) dan Segment 2 (Parameter ID)
$page = basename($urlParts[0] ?? 'dashboard');
$param_id = $urlParts[1] ?? null;

// Fallback: Kalau ada URL gaya lama (?page=agunan)
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $page = $_GET['page'];
}

// AUTO-INJECT ID & PAGE ke $_GET 
// Agar form_agunan.php bisa baca ID, dan auth_logic.php bisa highlight Sidebar dengan benar!
if ($param_id !== null) {
    $_GET['id'] = htmlspecialchars($param_id);
}
$_GET['page'] = $page; 

// ==========================================
// 3. LOAD LOGIC & COMPONENTS
// ==========================================
require_once __DIR__ . '/includes/auth_logic.php';
require_once __DIR__ . '/includes/asset_logic.php';
require_once __DIR__ . '/includes/components.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CESSIE Portal - BKK Jateng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fb; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="text-gray-800 flex h-screen overflow-hidden">

<?php if (!$is_logged_in): ?>
    <div class="min-h-screen w-full flex items-center justify-center px-4 bg-[#0b132b]">
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-2xl w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl"><i class="fas fa-shield-alt"></i></div>
                <h2 class="text-2xl font-extrabold text-gray-900">Portal Cessie</h2>
                <p class="text-gray-500 text-sm mt-2">Masuk untuk mengelola aset</p>
            </div>
            <?php if(isset($error_login)): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm font-bold text-center mb-4"><?= $error_login ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-5"><label class="block text-sm font-bold text-gray-700 mb-2">Kode Kantor</label><input type="text" name="kode_kantor" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none"></div>
                <div class="mb-6"><label class="block text-sm font-bold text-gray-700 mb-2">Password</label><input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-yellow-400 outline-none"></div>
                <button type="submit" name="login" class="w-full bg-[#1c2541] text-white font-bold py-3.5 rounded-xl hover:bg-[#0b132b] shadow-lg">Masuk System</button>
            </form>
        </div>
    </div>
<?php else: ?>
    
    <?php renderSidebar($page, $is_superadmin, $user_kode); ?>
    
    <main class="flex-1 flex flex-col h-full overflow-hidden relative z-10 bg-[#f4f7fb]">
        <?php renderHeader(); ?>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <?php 
                $allowed_pages = ['dashboard', 'data', 'form', 'view', 'pengajuan', 'calon_cessie', 'form_cessie', 'agunan', 'form_agunan'];
                
                if (in_array($page, $allowed_pages)) {
                    $view_name = ($page === 'data') ? 'data_aset' : (($page === 'form') ? 'form_aset' : (($page === 'view') ? 'view_detail' : $page));
                    $view_file = __DIR__ . "/includes/views/{$view_name}.php";
                    
                    if(file_exists($view_file)) {
                        include $view_file;
                    } else {
                        echo "
                        <div class='container mx-auto px-6 py-20 mt-10 text-center flex flex-col justify-center items-center'>
                            <h1 class='text-9xl font-extrabold text-blue-900'>404</h1>
                            <p class='text-gray-600 mt-8 mb-6 text-lg'>Error: File <b>{$view_name}.php</b> tidak ditemukan di folder views/.</p>
                            <a href='dashboard' class='bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg'>Kembali ke Dashboard</a>
                        </div>";
                    }
                } else {
                    // TAMPILAN 404 KHUSUS ADMIN (Kalau URL Ngaco)
                    echo "
                    <div class='container mx-auto px-6 py-20 mt-10 text-center min-h-[60vh] flex flex-col justify-center items-center'>
                        <h1 class='text-9xl font-extrabold text-blue-900'>404</h1>
                        <div class='bg-orange-500 text-white px-2 text-sm rounded rotate-12 absolute'>
                            Halaman Tidak Ditemukan
                        </div>
                        <p class='text-gray-600 mt-8 mb-6 text-lg'>Waduh brokuu, menu <b>{$page}</b> yang kamu cari nggak ada di panel admin ini.</p>
                        <a href='dashboard' class='bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg'>
                            Kembali ke Dashboard
                        </a>
                    </div>";
                }
            ?>
        </div>
    </main>
<?php endif; ?>

</body>
</html>