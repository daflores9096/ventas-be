<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    public function getAll(): array
    {
        return $this->productRepository->findAll();
    }

    public function create(string $name, float $price, int $stock = 0): array
    {
        return $this->productRepository->create([
            'name' => $name,
            'price' => $price,
            'stock' => $stock
        ]);
    }

    public function update(int $id, ?string $name, ?float $price, ?int $stock): bool
    {
        return $this->productRepository->update($id, $name, $price, $stock);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
