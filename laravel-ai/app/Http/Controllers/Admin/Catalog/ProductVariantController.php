<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\DTOs\Catalog\ProductVariantData;
use App\DTOs\Catalog\ProductVariantQueryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariant\IndexProductVariantRequest;
use App\Http\Requests\Admin\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\Admin\ProductVariant\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Catalog\ProductVariantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(IndexProductVariantRequest $request, Product $product, ProductVariantService $variantService): View
    {
        $queryData = ProductVariantQueryData::fromArray($request->validated());

        return view('admin.products.variants.index', [
            'product' => $product->load('category'),
            'variants' => $variantService->paginateForProduct($product, $queryData),
            'filters' => $queryData->toArray(),
        ]);
    }

    public function create(Product $product): View
    {
        return view('admin.products.variants.create', [
            'product' => $product,
        ]);
    }

    public function store(StoreProductVariantRequest $request, Product $product, ProductVariantService $variantService): RedirectResponse
    {
        $variantService->create($product, ProductVariantData::fromArray($request->validated()));

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Tạo biến thể thành công.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        return view('admin.products.variants.edit', [
            'product' => $product,
            'variant' => $variant,
        ]);
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant, ProductVariantService $variantService): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variantService->update($variant, ProductVariantData::fromArray($request->validated()));

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Cập nhật biến thể thành công.');
    }

    public function destroy(Product $product, ProductVariant $variant, ProductVariantService $variantService): RedirectResponse
    {
        $this->ensureVariantBelongsToProduct($product, $variant);

        $variantService->delete($variant);

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Xóa biến thể thành công.');
    }

    private function ensureVariantBelongsToProduct(Product $product, ProductVariant $variant): void
    {
        abort_unless($variant->product_id === $product->id, 404);
    }
}
