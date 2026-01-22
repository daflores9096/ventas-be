<?php
namespace App\Repositories;

use App\Utils\Database;
use PDO;
use Exception;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca un usuario por nombre de usuario.
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("
        SELECT users.*, roles.name AS role
        FROM users
        JOIN roles ON roles.id = users.role_id
        WHERE username = :username
        LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Crea un nuevo usuario y retorna su ID.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, password, email, role_id)
            VALUES (:username, :password, :email, :role_id)
        ");
        $stmt->execute([
            'username' => $data['username'],
            'password' => $data['password'],
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);
        return (int)$this->db->lastInsertId();
    }
}
