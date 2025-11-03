<?php
use App\Controllers\ProductController;
use App\Utils\AuthMiddleware;
use App\Utils\Response;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($path, '/api/products')) {
    $user = AuthMiddleware::verifyToken(); // Verifica token JWT
    AuthMiddleware::authorize(['admin', 'superadmin'], $user);

    $controller = new ProductController();

    if ($path === '/api/products' && $method === 'GET') {
        $controller->list();
    } elseif ($path === '/api/products' && $method === 'POST') {
        $controller->create();
    } elseif (preg_match('#^/api/products/(\d+)$#', $path, $matches)) {
        $id = (int)$matches[1];
        if ($method === 'PUT') {
            $controller->update($id);
        } elseif ($method === 'DELETE') {
            $controller->delete($id);
        } else {
            Response::error('Método no permitido', 405);
        }
    } else {
        Response::error('Ruta no encontrada', 404);
    }

    exit;
}
