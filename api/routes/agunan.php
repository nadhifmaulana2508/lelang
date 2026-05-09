<?php
require_once __DIR__ . '/../controllers/AgunanController.php';
require_once __DIR__ . '/../config/database.php';

$agunanController = new AgunanController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_GET['request'] ?? '', '/');
$endpoint = explode('/', $uri)[1] ?? ''; 

switch ($endpoint) {
    case 'create':
        if ($method === 'POST') $agunanController->create();
        break;
    case 'list':
        if ($method === 'GET') $agunanController->getAll();
        break;
    case 'detail':
        if ($method === 'GET') $agunanController->getDetail();
        break;
    case 'view': // <--- TAMBAHKAN INI UNTUK PUBLIC/USER
        if ($method === 'GET') $agunanController->getView();
        break;
    case 'update':
        if ($method === 'POST' || $method === 'PUT') $agunanController->update();
        break;
    case 'delete':
        if ($method === 'DELETE') $agunanController->delete();
        break;
    default:
        sendResponse(404, "Agunan endpoint tidak ditemukan");
}