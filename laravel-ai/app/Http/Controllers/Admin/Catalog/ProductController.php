<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\DTOs\Catalog\ProductData;
use App\DTOs\Catalog\ProductQueryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\IndexProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(
        IndexProductRequest $request,
        ProductService $productService,
        CategoryService $categoryService,
    ): View {
        $queryData = ProductQueryData::fromArray($request->validated());

        return view('admin.products.index', [
            'products' => $productService->paginate($queryData),
            'filters' => $queryData->toArray(),
            'categories' => $categoryService->allActiveForSelect(),
        ]);
    }

    public function create(CategoryService $categoryService): View
    {
        return view('admin.products.create', [
            'categories' => $categoryService->allActiveForSelect(),
        ]);
    }

    public function store(StoreProductRequest $request, ProductService $productService): RedirectResponse
    {
        $product = $productService->create(ProductData::fromArray($request->validated()));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Tạo sản phẩm thành công.');
    }

    public function show(Product $product): View
    {
        $product->load('category')->loadCount('variants');

        return view('admin.products.show', [
            'product' => $product,
            'latestVariants' => $product->variants()
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function edit(Product $product, CategoryService $categoryService): View
    {
        return view('admin.products.edit', [
            'product' => $product->load('category'),
            'categories' => $categoryService->allActiveForSelect(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, ProductService $productService): RedirectResponse
    {
        $product = $productService->update($product, ProductData::fromArray($request->validated()));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product, ProductService $productService): RedirectResponse
    {
        $productService->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm thành công.');
    }
}
