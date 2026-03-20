<?php

namespace App\Services\Catalog;

use App\DTOs\Catalog\CategoryData;
use App\DTOs\Catalog\CategoryQueryData;
use App\Exceptions\CannotDeleteCategoryException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * Trả về toàn bộ cây category theo thứ tự DFS (_lft ASC).
     * Khi có filter (search / status) thì trả về danh sách phẳng.
     *
     * @return Collection<int, Category>
     */
    public function getTree(?string $search = null, ?bool $isActive = null): Collection
    {
        $query = Category::query()->withCount('products')->orderBy('_lft');

        if ($search !== null) {
            $normalized = mb_strtolower($search);
            $query->where(function (Builder $b) use ($normalized): void {
                $b->whereRaw('LOWER(name) LIKE ?', ["%{$normalized}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$normalized}%"]);
            });
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->get();
    }

    /**
     * Danh sách categories để chọn làm parent trong form.
     * Trả về DFS order với depth để hiển thị thụt lề.
     * Nếu truyền $excludeId thì loại category đó + toàn bộ descendants.
     *
     * @return Collection<int, Category>
     */
    public function allForParentSelect(?string $excludeId = null): Collection
    {
        $all = Category::query()
            ->where('is_active', true)
            ->orderBy('_lft')
            ->get(['id', 'name', 'depth', '_lft', '_rgt']);

        if ($excludeId !== null) {
            $excluded = $all->firstWhere('id', $excludeId);
            if ($excluded !== null) {
                $all = $all->filter(
                    fn (Category $c): bool => !($c->_lft >= $excluded->_lft && $c->_rgt <= $excluded->_rgt),
                )->values();
            }
        }

        return $all;
    }

    /**
     * @return Collection<int, Category>
     */
    public function allActiveForSelect(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            'categories:active',
            now()->addMinutes(30),
            static fn (): array => Category::query()
                ->where('is_active', true)
                ->where('depth', '>', 0)
                ->orderBy('_lft')
                ->get(['id', 'name', 'depth'])
                ->toArray(),
        );

        return Category::hydrate($rows);
    }

    public function create(CategoryData $categoryData): Category
    {
        return DB::transaction(function () use ($categoryData): Category {
            $sortOrder = Category::query()
                ->where('parent_id', $categoryData->parentId)
                ->max('sort_order') + 1;

            $category = new Category();
            $category->fill(array_merge($categoryData->toPayload(), ['sort_order' => $sortOrder]));
            $category->save();

            $this->rebuildTree();

            Cache::forget('categories:active');

            return $category->refresh();
        });
    }

    public function update(Category $category, CategoryData $categoryData): Category
    {
        return DB::transaction(function () use ($category, $categoryData): Category {
            $isNewParent = $category->parent_id !== $categoryData->parentId;

            if ($isNewParent && $categoryData->parentId !== null) {
                $targetParent = Category::find($categoryData->parentId);
                if (
                    $targetParent !== null
                    && $targetParent->_lft >= $category->_lft
                    && $targetParent->_rgt <= $category->_rgt
                ) {
                    throw new \InvalidArgumentException('Không thể đặt category con làm category cha.');
                }
            }

            if ($isNewParent) {
                $sortOrder = Category::query()
                    ->where('parent_id', $categoryData->parentId)
                    ->max('sort_order') + 1;
                $category->sort_order = $sortOrder;
            }

            $category->fill($categoryData->toPayload());
            $category->save();

            if ($isNewParent) {
                $this->rebuildTree();
            }

            Cache::forget('categories:active');

            return $category->refresh();
        });
    }

    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            if ($category->products()->exists()) {
                throw CannotDeleteCategoryException::categoryInUse($category->name);
            }

            if ($category->children()->exists()) {
                throw CannotDeleteCategoryException::categoryHasChildren($category->name);
            }

            $category->delete();

            $this->rebuildTree();

            Cache::forget('categories:active');
        });
    }

    public function moveUp(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            $prev = Category::query()
                ->where('parent_id', $category->parent_id)
                ->where('sort_order', '<', $category->sort_order)
                ->orderByDesc('sort_order')
                ->first();

            if ($prev === null) {
                return;
            }

            [$category->sort_order, $prev->sort_order] = [$prev->sort_order, $category->sort_order];
            $category->saveQuietly();
            $prev->saveQuietly();

            $this->rebuildTree();
        });
    }

    public function moveDown(Category $category): void
    {
        DB::transaction(function () use ($category): void {
            $next = Category::query()
                ->where('parent_id', $category->parent_id)
                ->where('sort_order', '>', $category->sort_order)
                ->orderBy('sort_order')
                ->first();

            if ($next === null) {
                return;
            }

            [$category->sort_order, $next->sort_order] = [$next->sort_order, $category->sort_order];
            $category->saveQuietly();
            $next->saveQuietly();

            $this->rebuildTree();
        });
    }

    /**
     * Rebuild toàn bộ _lft, _rgt, depth từ parent_id + sort_order.
     * Gọi sau bất kỳ thay đổi cấu trúc cây nào.
     */
    private function rebuildTree(): void
    {
        $all = Category::query()->orderBy('sort_order')->get(['id', 'parent_id', 'sort_order']);

        /** @var array<string, array{_lft: int, _rgt: int, depth: int}> $updates */
        $updates = [];
        $counter = 0;
        $this->computePositions($all, null, $counter, 0, $updates);

        foreach ($updates as $id => $pos) {
            DB::table('categories')->where('id', $id)->update($pos);
        }
    }

    /**
     * @param  Collection<int, Category>                                    $all
     * @param  array<string, array{_lft: int, _rgt: int, depth: int}>       $updates
     */
    private function computePositions(
        Collection $all,
        ?string $parentId,
        int &$counter,
        int $depth,
        array &$updates,
    ): void {
        $children = $all
            ->filter(fn (Category $n): bool => $n->parent_id === $parentId)
            ->sortBy('sort_order');

        foreach ($children as $node) {
            $lft = ++$counter;
            $this->computePositions($all, (string) $node->id, $counter, $depth + 1, $updates);
            $rgt = ++$counter;

            $updates[(string) $node->id] = ['_lft' => $lft, '_rgt' => $rgt, 'depth' => $depth];
        }
    }
}
