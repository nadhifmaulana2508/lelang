<?php

// Pastikan nama file controller-nya sudah sesuai dengan yang ada di folder kamu ya
require_once __DIR__ . '/../controllers/DummyController.php'; // Asumsi isi classnya CatalogController
require_once __DIR__ . '/../controllers/PengajuanController.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middlewares/authMiddleware.php';

$catalogController = new CatalogController($pdo); 
$pengajuanController = new PengajuanController($pdo); 
$method = $_SERVER['REQUEST_METHOD'];

// Asumsi .htaccess memecah URL menjadi array segment. Ex: /api/asset/home -> ['asset', 'home']
$segments = $_GET['segments'] ?? [];
$module = $segments[0] ?? ''; // ex: 'asset', 'pengajuan'
$action = $segments[1] ?? ''; // ex: 'detail', 'home', 'store', 'track-view'

switch ($method) {
    case 'GET':
        if ($action === 'detail') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $catalogController->getDetail($id);
            } else {
                sendResponse(400, "ID tidak disediakan");
            }

        } elseif ($action === 'home') {
            $catalogController->getHome();

        } else {
            $jenis = $_GET['jenis_jaminan'] ?? null;
            if ($jenis) {
                $catalogController->getByJenisAgunan($jenis);
            } else {
                $catalogController->getAll();
            }
        }
        break;

    case 'POST':
        // ✅ Endpoint: /api/pengajuan/store
        if ($action === 'store') {
            $pengajuanController->store();
        } 
        // ✅ Endpoint: /api/asset/track-view atau /api/asset/view
        elseif ($action === 'track-view' || $action === 'view') {
            $catalogController->trackView();
        } 
        else {
            sendResponse(404, "Endpoint POST tidak ditemukan");
        }
        break;

    default:
        sendResponse(405, "Method tidak diizinkan");
        break;
}