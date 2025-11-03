<?php
use App\Controllers\UserController;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/api/users' && $method === 'GET') {
    (new UserController())->list();
    exit;
}

if ($path === '/api/users' && $method === 'POST') {
    (new UserController())->create();
    exit;
}
