<?php
namespace App\Services;

use App\Repositories\SaleRepository;
use App\Repositories\ProductRepository;
use App\Utils\Database;
use PDO;
use Exception;

class SaleService
{
    private SaleRepository $saleRepository;
    private ProductRepository $productRepository;
    private PDO $db;

    public function __construct()
    {
        $this->saleRepository = new SaleRepository();
        $this->productRepository = new ProductRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Crea una venta con varios productos.
     */
    public function createSale(int $userId, array $items): int
    {
        try {
            $this->db->beginTransaction();

            $total = 0;
            foreach ($items as $item) {
                if (!isset($item['product_id'], $item['quantity'])) {
                    throw new Exception('Formato de ítem inválido');
                }

                $product = $this->productRepository->findById($item['product_id']);
                if (!$product) {
                    throw new Exception("Producto ID {$item['product_id']} no existe");
                }

                if ($product['stock'] < $item['quantity']) {
                    throw new Exception("Stock insuficiente para {$product['name']}");
                }

                $total += $product['price'] * $item['quantity'];

                // Actualizar stock
                $this->productRepository->updateStock($item['product_id'], $product['stock'] - $item['quantity']);
            }

            $saleId = $this->saleRepository->createSale($userId, $total);

            foreach ($items as $item) {
                $product = $this->productRepository->findById($item['product_id']);
                $this->saleRepository->addSaleItem($saleId, $product['id'], $item['quantity'], $product['price']);
            }

            $this->db->commit();
            return $saleId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene las ventas de un usuario (o todas si es admin/superadmin).
     */
    public function getSalesByUser(int $userId, int $roleId): array
    {
        if (in_array($roleId, [1, 2])) {
            return $this->saleRepository->findAll();
        }
        return $this->saleRepository->findByUser($userId);
    }
}
