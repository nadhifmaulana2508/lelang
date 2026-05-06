<?php

require_once __DIR__ . '/helpers/response.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = $_GET['request'] ?? '';
$segments = explode('/', trim($request, '/'));

$endpoint = $segments[0] ?? ''; // ex: 'catalog'
switch ($endpoint) {
    case '':
        sendResponse(200, "API is running");
        break;
    case 'users':
        require __DIR__ . '/routes/user.php';
        break;
    case 'catalog':
        $_GET['segments'] = $segments; // Pass segments ke route
        require __DIR__ . '/routes/catalog.php';
        break;
    case 'asset':
        $_GET['segments'] = $segments; // Pass segments ke route
        require __DIR__ . '/routes/asset.php';
        break;
    case 'register':
        require __DIR__ . '/routes/register.php';
        break;
    case 'auth':
        require __DIR__ . '/routes/auth.php';
        break;
    default:
        sendResponse(404, "Endpoint tidak ditemukan");
        break;
}
