<?php
namespace App\Utils;

use App\Utils\Jwt;
use Exception;

class AuthMiddleware
{
    /**
     * Verifica que el request tenga un JWT válido.
     * Retorna los datos del usuario si es válido o detiene la ejecución con error.
     */
    public static function verifyToken(): object
    {
        $headers = apache_request_headers();

        if (!isset($headers['Authorization'])) {
            Response::error('Token no proporcionado', 401);
        }

        $authHeader = $headers['Authorization'];
        if (!str_starts_with($authHeader, 'Bearer ')) {
            Response::error('Formato de token inválido', 400);
        }

        $token = trim(str_replace('Bearer', '', $authHeader));

        try {
            $decoded = Jwt::verify($token);
            return $decoded;
        } catch (Exception $e) {
            Response::error('Token inválido o expirado: ' . $e->getMessage(), 401);
        }
    }

    /**
     * Verifica que el usuario tenga un rol autorizado.
     *
     * @param array|string $allowedRoles Ej: ['admin', 'superadmin'] o 'admin'
     */
    public static function authorize(array|string $allowedRoles, object $user): void
    {
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        // Mapear IDs a nombres de rol (según tu base de datos)
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
