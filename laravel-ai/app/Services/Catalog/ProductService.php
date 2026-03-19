<?php

namespace App\Services\Catalog;

use App\DTOs\Catalog\ProductData;
use App\DTOs\Catalog\ProductQueryData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function paginate(ProductQueryData $queryData): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category')
            ->withCount('variants');

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$normalizedSearch}%"]);
            });
        }

        if ($queryData->categoryId !== null) {
            $query->where('category_id', $queryData->categoryId);
        }

        if ($queryData->isActive !== null) {
            $query->where('is_active', $queryData->isActive);
        }

        $query
            ->orderBy($queryData->sortBy, $queryData->sortDirection)
            ->orderBy('id');

        return $query
            ->paginate($queryData->perPage)
            ->withQueryString();
    }

    public function create(ProductData $productData): Product
    {
        return DB::transaction(function () use ($productData): Product {
            return Product::query()->create($productData->toPayload());
        });
    }

    public function update(Product $product, ProductData $productData): Product
    {
        return DB::transaction(function () use ($product, $productData): Product {
            $product->fill($productData->toPayload());
            $product->save();

            return $product->refresh();
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product): void {
            $product->delete();
        });
    }
}
