<?php
namespace App\Utils;

use App\Utils\Response;
use App\Utils\Jwt;
use Exception;

class AuthMiddleware
{
    private static function getHeaders(): array
    {
        // Preferencia si existe
        if (function_exists('apache_request_headers')) {
            return apache_request_headers();
        }

        // Alternativa universal
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public static function verifyToken(): object
    {
        $headers = self::getHeaders();

        if (!isset($headers['Authorization'])) {
            Response::error('Token no proporcionado', 401);
        }

        $authHeader = $headers['Authorization'];

        if (!str_starts_with($authHeader, 'Bearer ')) {
            Response::error('Formato de token inválido', 400);
        }

        $token = trim(str_replace('Bearer', '', $authHeader));

        try {
            return Jwt::verify($token);
        } catch (Exception $e) {
            Response::error('Token inválido o expirado: ' . $e->getMessage(), 401);
        }
    }

    public static function authorize(array|string $allowedRoles, object $user): void
    {
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        $roleMap = [
            1 => 'superadmin',
            2 => 'admin',
            3 => 'user'
        ];

        $userRoleName = $roleMap[$user->role_id] ?? 'user';

        if (!in_array($userRoleName, $allowedRoles, true)) {
            Response::error('No tienes permiso para acceder a este recurso', 403);
        }
    }
}
