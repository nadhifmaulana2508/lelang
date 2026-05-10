<?php
// ==========================================
// 1. DYNAMIC BASE URL (Solusi Local vs Server)
// ==========================================
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$folder = dirname($_SERVER['SCRIPT_NAME']);
// Rapikan path jika berada di root server
$folder = ($folder == '/' || $folder == '\\') ? '' : $folder;

// Jadikan konstanta agar bisa dipanggil di SEMUA file (header, footer, pages)
define('BASE_URL', $protocol . "://" . $domain . $folder);

// ==========================================
// 2. CORE ROUTING (INTERCEPT API & CLIENT)
// ==========================================
// Ambil URL, pastikan tidak ada slash berlebih di akhir
$url = isset($_GET['url']) && !empty($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';

// Fallback khusus aaPanel (biasanya melempar ke ?s=)
if ($url === 'home' && isset($_GET['s']) && !empty($_GET['s'])) {
    $url = rtrim($_GET['s'], '/');
}

$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', ltrim($url, '/'));

// --- INTERCEPT ROUTE CLIENT (ADMIN) ---
if ($urlParts[0] === 'client') {
    $_GET['page'] = isset($urlParts[1]) && !empty($urlParts[1]) ? $urlParts[1] : 'dashboard';
    if (isset($urlParts[2])) $_GET['id'] = $urlParts[2];
    require "client/index.php";
    exit;
}

// --- INTERCEPT ROUTE API ---
if ($urlParts[0] === 'api') {
    $_GET['request'] = implode('/', array_slice($urlParts, 1));
    require "api/index.php";
    exit;
}

// ==========================================
// 3. LOAD DEPENDENCIES & HEADER (PAGES)
// ==========================================
// require_once './api/config/config.php';
include("views/header.php");
include("views/navbar.php");

// ==========================================
// 4. CORE ROUTING LOGIC (PAGES)
// ==========================================
// KEAMANAN: Gunakan basename() agar user tidak bisa melakukan path traversal
$page = basename($urlParts[0] ?? 'home');
$param = $urlParts[1] ?? null;

$path = "pages/" . $page . ".php";

// ==========================================
// 4. RENDER HALAMAN
// ==========================================
if (file_exists($path)) {
    // Buat variabel id bisa diakses menggunakan $_GET['id'] di dalam file page
    if ($param !== null) {
        $_GET['id'] = htmlspecialchars($param); // Sanitize parameter
    }
    
    // Panggil halaman
    include $path;

} else {
    // 404 Page (Sudah dipercantik dengan Tailwind)
    echo "
    <div class='container mx-auto px-6 py-20 mt-10 text-center min-h-[60vh] flex flex-col justify-center items-center'>
        <h1 class='text-9xl font-extrabold text-blue-900'>404</h1>
        <div class='bg-orange-500 text-white px-2 text-sm rounded rotate-12 absolute'>
            Halaman Tidak Ditemukan
        </div>
        <p class='text-gray-600 mt-8 mb-6 text-lg'>Waduh brokuu, aset atau halaman yang kamu cari kayaknya udah laku atau nggak ada nih.</p>
        <a href='" . BASE_URL . "/home' class='bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition duration-300 shadow-lg'>
            Kembali ke Beranda
        </a>
    </div>";
}


// ==========================================
// 5. LOAD SCRIPT & FOOTER
// ==========================================
include("views/script.php");
include("views/footer.php");
?>