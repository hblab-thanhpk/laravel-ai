<?php

namespace App\Services\Catalog;

use App\DTOs\Catalog\ProductVariantData;
use App\DTOs\Catalog\ProductVariantQueryData;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    public function paginateForProduct(Product $product, ProductVariantQueryData $queryData): LengthAwarePaginator
    {
        $query = ProductVariant::query()->where('product_id', $product->getKey());

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(sku) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(size) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(color) LIKE ?', ["%{$normalizedSearch}%"]);
            });
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

    public function create(Product $product, ProductVariantData $variantData): ProductVariant
    {
        return DB::transaction(function () use ($product, $variantData): ProductVariant {
            return ProductVariant::query()->create([
                'product_id' => (string) $product->getKey(),
                ...$variantData->toPayload(),
            ]);
        });
    }

    public function update(ProductVariant $variant, ProductVariantData $variantData): ProductVariant
    {
        return DB::transaction(function () use ($variant, $variantData): ProductVariant {
            $variant->fill($variantData->toPayload());
            $variant->save();

            return $variant->refresh();
        });
    }

    public function delete(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant): void {
            $variant->delete();
        });
    }
}
