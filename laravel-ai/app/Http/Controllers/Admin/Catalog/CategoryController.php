<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\DTOs\Catalog\CategoryData;
use App\DTOs\Catalog\CategoryQueryData;
use App\Exceptions\CannotDeleteCategoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\IndexCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Catalog\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(IndexCategoryRequest $request, CategoryService $categoryService): View
    {
        $queryData = CategoryQueryData::fromArray($request->validated());

        return view('admin.categories.index', [
            'categories' => $categoryService->paginate($queryData),
            'filters' => $queryData->toArray(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request, CategoryService $categoryService): RedirectResponse
    {
        $category = $categoryService->create(CategoryData::fromArray($request->validated()));

        return redirect()
            ->route('admin.categories.show', $category)
            ->with('success', 'Tạo danh mục thành công.');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', [
            'category' => $category->loadCount('products'),
        ]);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category, CategoryService $categoryService): RedirectResponse
    {
        $category = $categoryService->update($category, CategoryData::fromArray($request->validated()));

        return redirect()
            ->route('admin.categories.show', $category)
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category, CategoryService $categoryService): RedirectResponse
    {
        try {
            $categoryService->delete($category);
        } catch (CannotDeleteCategoryException $exception) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}
