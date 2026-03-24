<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Catalog\ProductData;
use App\DTOs\Catalog\ProductQueryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\IndexProductRequest;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request, ProductService $productService): JsonResponse
    {
        $queryData = ProductQueryData::fromArray($request->validated());

        $paginator = $productService->paginate($queryData);

        return $this->successResponse('Danh sách sản phẩm.', [
            'items' => ProductResource::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'variants']);

        return $this->successResponse(
            'Chi tiết sản phẩm.',
            (new ProductResource($product))->resolve(),
        );
    }

    public function store(StoreProductRequest $request, ProductService $productService): JsonResponse
    {
        $product = $productService->create(
            ProductData::fromArray($request->validated()),
        );

        $product->load('category');

        return $this->successResponse(
            'Tạo sản phẩm thành công.',
            (new ProductResource($product))->resolve(),
            201,
        );
    }

    public function update(UpdateProductRequest $request, Product $product, ProductService $productService): JsonResponse
    {
        $product = $productService->update(
            $product,
            ProductData::fromArray($request->validated()),
        );

        $product->load('category');

        return $this->successResponse(
            'Cập nhật sản phẩm thành công.',
            (new ProductResource($product))->resolve(),
        );
    }

    public function destroy(Product $product, ProductService $productService): JsonResponse
    {
        $productService->delete($product);

        return $this->successResponse('Xoá sản phẩm thành công.', status: 200);
    }
}
