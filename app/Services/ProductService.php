<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Models\v1\Product;
use App\Repositories\ProductRepository\Interfaces\IProductRepository;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private IProductRepository $productRepository
    ) {}

    public function create(ProductDTO $dto): Product
    {
        $data = $dto->toArray();
        $data['sku'] = Str::uuid();
        $data['tenant_id'] = auth()->user()->tenant_id;

        return $this->productRepository->create($data);
    }
    public function update(int $id, ProductDTO $dto): Product
    {
        return $this->productRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function all()
    {
        return $this->productRepository->all();
    }
}
