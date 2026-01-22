<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Utils\Response;
use App\Utils\Jwt;
use Exception;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Inicia sesión y devuelve un JWT si las credenciales son válidas.
     * Endpoint: POST /api/auth/login
     */
    public function login(): void
    {
        // Obtener JSON del cuerpo del request
        $input = json_decode(file_get_contents('php://input'), true);

        $username = $input['username'] ?? null;
        $password = $input['password'] ?? null;

        if (!$username || !$password) {
            Response::json(['message' => 'Username y password requeridos'], 400);
            return;
        }

        try {
            $user = $this->authService->validateCredentials($username, $password);

            if (!$user) {
                Response::json(['message' => 'Credenciales inválidas'], 401);
                return;
            }

            // Generar token
            $token = Jwt::encode([
                'sub' => $user['id'],
                'username' => $user['username'],
                'role_id' => $user['role_id'],
                'role' => $user['role']
            ]);

            Response::json([
                'status' => 'success',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
        } catch (Exception $e) {
            Response::json(['message' => 'Error en login: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Registra un nuevo usuario.
     * Endpoint: POST /api/auth/register
     */
    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $username = $input['username'] ?? null;
        $password = $input['password'] ?? null;
        $email = $input['email'] ?? null;
        $role_id = $input['role_id'] ?? 3; // usuario normal por defecto

        if (!$username || !$password || !$email) {
            Response::json(['message' => 'Campos incompletos'], 400);
            return;
        }

        try {
            $user = $this->authService->registerUser($username, $password, $email, $role_id);
            Response::json([
                'status' => 'success',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            Response::json(['message' => 'Error en registro: ' . $e->getMessage()], 500);
        }
    }
}
