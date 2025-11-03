<?php
use App\Controllers\SaleController;
use App\Utils\AuthMiddleware;
use App\Utils\Response;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($path, '/api/sales')) {
    $user = AuthMiddleware::verifyToken();
    AuthMiddleware::authorize(['user', 'admin', 'superadmin'], $user);

    $controller = new SaleController();

    if ($path === '/api/sales' && $method === 'POST') {
        $controller->create($user);
    } elseif ($path === '/api/sales' && $method === 'GET') {
        $controller->list($user);
    } else {
        Response::error('Ruta no encontrada', 404);
    }

    exit;
}
