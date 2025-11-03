<?php
namespace App\Repositories;

use App\Utils\Database;
use PDO;

class SaleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createSale(int $userId, float $total): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sales (user_id, total) VALUES (:user_id, :total)
        ");
        $stmt->execute(['user_id' => $userId, 'total' => $total]);
        return (int)$this->db->lastInsertId();
    }

    public function addSaleItem(int $saleId, int $productId, int $quantity, float $price): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO sale_items (sale_id, product_id, quantity, price)
            VALUES (:sale_id, :product_id, :quantity, :price)
        ");
        $stmt->execute([
            'sale_id' => $saleId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, u.username 
            FROM sales s
            JOIN users u ON u.id = s.user_id
            ORDER BY s.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, si.product_id, si.quantity, si.price
            FROM sales s
            JOIN sale_items si ON si.sale_id = s.id
            WHERE s.user_id = :user_id
            ORDER BY s.id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
