<?php
namespace App\Controllers;

use App\Services\SaleService;
use App\Utils\Response;
use App\Utils\AuthMiddleware;
use Exception;

class SaleController
{
    private SaleService $saleService;

    public function __construct()
    {
        $this->saleService = new SaleService();
    }

    /**
     * Registrar una venta
     * POST /api/sales
     */
    public function create(object $user): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $items = $input['items'] ?? [];
        if (empty($items)) {
            Response::error('Debe incluir al menos un producto en la venta', 400);
        }

        try {
            $saleId = $this->saleService->createSale($user->sub, $items);
            Response::json([
                'status' => 'success',
                'data' => ['sale_id' => $saleId]
            ], 201);
        } catch (Exception $e) {
            Response::error('Error al registrar la venta: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener las ventas del usuario autenticado
     * GET /api/sales
     */
    public function list(object $user): void
    {
        try {
            $sales = $this->saleService->getSalesByUser($user->sub, $user->role_id);
            Response::json(['status' => 'success', 'data' => $sales]);
        } catch (Exception $e) {
            Response::error('Error al obtener ventas: ' . $e->getMessage(), 500);
        }
    }
}
