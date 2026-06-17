<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductRequests\StoreProductRequest;
use App\Http\Requests\V1\ProductRequests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->productService->all());
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->find($id);

        if (! $product) {
            return response()->json(['message' => 'Ürün bulunamadı.'], 404);
        }

        return response()->json($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $product = $this->productService->create($dto);

        return response()->json($product, 201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $product = $this->productService->update($id, $dto);

        return response()->json($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json(null, 204);
    }
}
