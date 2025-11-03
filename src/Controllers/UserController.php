<?php
namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Utils\AuthMiddleware;
use App\Utils\Response;

class UserController {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function list() {
        AuthMiddleware::requireRole(['admin', 'superadmin']);
        $data = $this->repo->listAll();
        Response::success($data);
    }

    public function create() {
        AuthMiddleware::requireRole(['admin', 'superadmin']);
        $input = json_decode(file_get_contents('php://input'), true) ?: [];

        if (!isset($input['username'], $input['password'], $input['role_id'])) {
            Response::error('username, password, role_id required', 400);
        }

        $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
        $id = $this->repo->create($input);
        Response::success(['id' => $id], 201);
    }
}
