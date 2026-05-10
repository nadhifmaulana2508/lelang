<?php
// Deteksi apakah dijalankan di localhost (XAMPP/Laragon)
$is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1');

// Set Base URL berdasarkan Environment
if (!defined('BASE_URL')) {
    if ($is_localhost) {
        define('BASE_URL', 'http://localhost/lelang_bkkjtg');
    } else {
        define('BASE_URL', 'https://lelang.bkkjateng.co.id'); // URL Production Asli
    }
}

// Buat konstanta khusus untuk API
if (!defined('API_URL')) {
    define('API_URL', BASE_URL . '/api');
}
?>