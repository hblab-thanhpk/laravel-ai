<?php

namespace App\Services\Catalog;

use App\DTOs\Catalog\CategoryData;
use App\DTOs\Catalog\CategoryQueryData;
use App\Exceptions\CannotDeleteCategoryException;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function paginate(CategoryQueryData $queryData): LengthAwarePaginator
    {
        $query = Category::query()->withCount('products');

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$normalizedSearch}%"]);
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

    public function create(CategoryData $categoryData): Category
    {
        return DB::transaction(function () use ($categoryData): Category {
            return Category::query()->create($categoryData->toPayload());
        });
    }

    public function update(Category $category, CategoryData $categoryData): Category
    {
        return DB::transaction(function () use ($category, $categoryData): Category {
            $category->fill($categoryData->toPayload());
            $category->save();

            return $category->refresh();
        });
    }

    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            if ($category->products()->exists()) {
                throw CannotDeleteCategoryException::categoryInUse($category->name);
            }

            $category->delete();
        });
    }
}
