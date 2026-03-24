<?php

use App\Http\Controllers\Admin\Access\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\Access\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\Catalog\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\Catalog\ProductController as AdminProductController;
use App\Http\Controllers\Admin\Catalog\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\Order\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('admin.login.form')
        ->middleware('guest');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('admin.login')
        ->middleware(['guest', 'throttle:admin-login']);

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout')
        ->middleware('auth');

    Route::middleware('admin')->group(function (): void {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::prefix('users')->name('admin.users.')->group(function (): void {
            Route::get('/', [AdminUserController::class, 'index'])
                ->middleware('permission:users_view')
                ->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])
                ->middleware('permission:users_create')
                ->name('create');
            Route::post('/', [AdminUserController::class, 'store'])
                ->middleware('permission:users_create')
                ->name('store');
            Route::get('/{user}', [AdminUserController::class, 'show'])
                ->middleware('permission:users_view')
                ->name('show');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])
                ->middleware('permission:users_update')
                ->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])
                ->middleware('permission:users_update')
                ->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])
                ->middleware('permission:users_delete')
                ->name('destroy');
        });

        Route::prefix('roles')->name('admin.roles.')->group(function (): void {
            Route::get('/', [AdminRoleController::class, 'index'])
                ->middleware('permission:roles_view')
                ->name('index');
            Route::get('/create', [AdminRoleController::class, 'create'])
                ->middleware('permission:roles_create')
                ->name('create');
            Route::post('/', [AdminRoleController::class, 'store'])
                ->middleware('permission:roles_create')
                ->name('store');
            Route::get('/{role}', [AdminRoleController::class, 'show'])
                ->middleware('permission:roles_view')
                ->name('show');
            Route::get('/{role}/edit', [AdminRoleController::class, 'edit'])
                ->middleware('permission:roles_update')
                ->name('edit');
            Route::put('/{role}', [AdminRoleController::class, 'update'])
                ->middleware('permission:roles_update')
                ->name('update');
            Route::delete('/{role}', [AdminRoleController::class, 'destroy'])
                ->middleware('permission:roles_delete')
                ->name('destroy');
        });

        Route::prefix('permissions')->name('admin.permissions.')->group(function (): void {
            Route::get('/', [AdminPermissionController::class, 'index'])
                ->middleware('permission:permissions_view')
                ->name('index');
            Route::get('/create', [AdminPermissionController::class, 'create'])
                ->middleware('permission:permissions_create')
                ->name('create');
            Route::post('/', [AdminPermissionController::class, 'store'])
                ->middleware('permission:permissions_create')
                ->name('store');
            Route::get('/{permission}', [AdminPermissionController::class, 'show'])
                ->middleware('permission:permissions_view')
                ->name('show');
            Route::get('/{permission}/edit', [AdminPermissionController::class, 'edit'])
                ->middleware('permission:permissions_update')
                ->name('edit');
            Route::put('/{permission}', [AdminPermissionController::class, 'update'])
                ->middleware('permission:permissions_update')
                ->name('update');
            Route::delete('/{permission}', [AdminPermissionController::class, 'destroy'])
                ->middleware('permission:permissions_delete')
                ->name('destroy');
        });

        Route::prefix('categories')->name('admin.categories.')->group(function (): void {
            Route::get('/', [AdminCategoryController::class, 'index'])
                ->middleware('permission:categories_view')
                ->name('index');
            Route::get('/create', [AdminCategoryController::class, 'create'])
                ->middleware('permission:categories_create')
                ->name('create');
            Route::post('/', [AdminCategoryController::class, 'store'])
                ->middleware('permission:categories_create')
                ->name('store');
            Route::get('/{category}', [AdminCategoryController::class, 'show'])
                ->middleware('permission:categories_view')
                ->name('show');
            Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])
                ->middleware('permission:categories_update')
                ->name('edit');
            Route::put('/{category}', [AdminCategoryController::class, 'update'])
                ->middleware('permission:categories_update')
                ->name('update');
            Route::post('/{category}/move', [AdminCategoryController::class, 'move'])
                ->middleware('permission:categories_update')
                ->name('move');
            Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])
                ->middleware('permission:categories_delete')
                ->name('destroy');
        });

        Route::prefix('products')->name('admin.products.')->group(function (): void {
            Route::get('/', [AdminProductController::class, 'index'])
                ->middleware('permission:products_view')
                ->name('index');
            Route::get('/create', [AdminProductController::class, 'create'])
                ->middleware('permission:products_create')
                ->name('create');
            Route::post('/', [AdminProductController::class, 'store'])
                ->middleware('permission:products_create')
                ->name('store');
            Route::get('/{product}', [AdminProductController::class, 'show'])
                ->middleware('permission:products_view')
                ->name('show');
            Route::get('/{product}/edit', [AdminProductController::class, 'edit'])
                ->middleware('permission:products_update')
                ->name('edit');
            Route::put('/{product}', [AdminProductController::class, 'update'])
                ->middleware('permission:products_update')
                ->name('update');
            Route::delete('/{product}', [AdminProductController::class, 'destroy'])
                ->middleware('permission:products_delete')
                ->name('destroy');

            Route::prefix('/{product}/variants')->name('variants.')->scopeBindings()->group(function (): void {
                Route::get('/', [AdminProductVariantController::class, 'index'])
                    ->middleware('permission:product_variants_view')
                    ->name('index');
                Route::get('/create', [AdminProductVariantController::class, 'create'])
                    ->middleware('permission:product_variants_create')
                    ->name('create');
                Route::post('/', [AdminProductVariantController::class, 'store'])
                    ->middleware('permission:product_variants_create')
                    ->name('store');
                Route::get('/{variant}/edit', [AdminProductVariantController::class, 'edit'])
                    ->middleware('permission:product_variants_update')
                    ->name('edit');
                Route::put('/{variant}', [AdminProductVariantController::class, 'update'])
                    ->middleware('permission:product_variants_update')
                    ->name('update');
                Route::delete('/{variant}', [AdminProductVariantController::class, 'destroy'])
                    ->middleware('permission:product_variants_delete')
                    ->name('destroy');
            });
        });

        Route::prefix('orders')->name('admin.orders.')->group(function (): void {
            Route::get('/', [AdminOrderController::class, 'index'])
                ->middleware('permission:orders_view')
                ->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])
                ->middleware('permission:orders_view')
                ->name('show');
            Route::get('/{order}/edit', [AdminOrderController::class, 'edit'])
                ->middleware('permission:orders_update')
                ->name('edit');
            Route::put('/{order}', [AdminOrderController::class, 'update'])
                ->middleware('permission:orders_update')
                ->name('update');
            Route::delete('/{order}', [AdminOrderController::class, 'destroy'])
                ->middleware('permission:orders_delete')
                ->name('destroy');
        });
    });
});
