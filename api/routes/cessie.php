<?php

require_once __DIR__ . '/../controllers/CessieController.php';
require_once __DIR__ . '/../config/database.php';

$cessieController = new CessieController($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_GET['request'] ?? '', '/');
// Jika URL-nya: /api/cessie/create, maka $endpoint = 'create'
$endpoint = explode('/', $uri)[1] ?? ''; 

switch ($endpoint) {
    // 1. GET ALL
    case '':
    case 'list':
        if ($method === 'GET') {
            $cessieController->getAll();
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    // 2. GET DETAIL BY ID
    case 'detail':
        if ($method === 'GET') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $cessieController->getDetail($id);
            } else {
                sendResponse(400, "ID tidak disertakan");
            }
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    // 3. CREATE DATA
    case 'create':
        if ($method === 'POST') {
            // Kita pakai form-data/x-www-form-urlencoded karena di controller pakai $_POST
            $cessieController->create();
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    // 4. UPDATE DATA
    case 'update':
        if ($method === 'POST' || $method === 'PUT') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $cessieController->update($id);
            } else {
                sendResponse(400, "ID tidak disertakan");
            }
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    // 5. DELETE DATA
    case 'delete':
        if ($method === 'DELETE' || $method === 'POST') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $cessieController->delete($id);
            } else {
                sendResponse(400, "ID tidak disertakan");
            }
        } else {
            sendResponse(405, "Metode tidak diizinkan");
        }
        break;

    default:
        sendResponse(404, "Cessie endpoint tidak ditemukan");
}