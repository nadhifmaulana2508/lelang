<?php
session_start();
require_once __DIR__ . '/../../api/config/database.php';

if (isset($_POST['login'])) {
    $kode_kantor = $_POST['kode_kantor'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($kode_kantor !== '' && $pass === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['kode_kantor'] = $kode_kantor;
        $_SESSION['role'] = ($kode_kantor === '000') ? 'superadmin' : 'cabang';
        header("Location: " . BASE_URL . "/client/dashboard");
        exit;
    } else {
        $error_login = "Kode Kantor atau Password salah!";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: " . BASE_URL . "/client/dashboard");
    exit;
}

$is_logged_in = $_SESSION['logged_in'] ?? false;
$user_kode = $_SESSION['kode_kantor'] ?? '';
$is_superadmin = ($_SESSION['role'] ?? '') === 'superadmin';
$page = $_GET['page'] ?? 'dashboard';