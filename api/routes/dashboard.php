<?php
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../config/database.php';

$dashboardController = new DashboardController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_GET['request'] ?? '', '/');
$endpoint = explode('/', $uri)[1] ?? ''; 

switch ($endpoint) {
    // URL: /api/dashboard/stats
    case 'stats':
        if ($method === 'GET') {
            $dashboardController->getStats();
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    default:
        sendResponse(404, "Dashboard endpoint tidak ditemukan");
}