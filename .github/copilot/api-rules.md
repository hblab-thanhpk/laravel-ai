# API Rules — REST API (Laravel Sanctum)

## Overview
- Public API phục vụ Frontend SPA / Mobile App.
- Tất cả response: **JSON only** — không render Blade view.
- Auth: **Laravel Sanctum** (Bearer token).

---

## Response Format (BẮT BUỘC)

### Success
```json
{
  "status": "success",
  "message": "Thao tác thành công",
  "data": {}
}
```

### Error
```json
{
  "status": "error",
  "message": "Mô tả lỗi",
  "errors": {}
}
```

### Paginated
```json
{
  "status": "success",
  "message": "",
  "data": {
    "items": [],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 72
    }
  }
}
```

---

## Route Structure

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::middleware('auth:sanctum')
             ->post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('orders',   OrderController::class);
    });
});
```

---

## API Controller Rules

- Extends `App\Http\Controllers\Controller`.
- Trả về `JsonResponse` — không dùng `redirect()`.
- HTTP status codes phải đúng ngữ nghĩa:
  - `200` OK (index, show, update)
  - `201` Created (store)
  - `204` No Content (destroy)
  - `401` Unauthenticated
  - `403` Forbidden
  - `404` Not Found
  - `422` Validation Error

```php
public function store(StoreProductRequest $request): JsonResponse
{
    $product = $this->productService->create(
        CreateProductDTO::fromRequest($request)
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'Tạo sản phẩm thành công',
        'data'    => new ProductResource($product),
    ], 201);
}
```

---

## API Resource (Transformer)

- Luôn dùng **Laravel API Resource** — không trả `$model->toArray()`.
- Không expose trường nhạy cảm (password, remember_token, ...).

```php
// app/Http/Resources/ProductResource.php
public function toArray(Request $request): array
{
    return [
        'id'    => $this->id,
        'name'  => $this->name,
        'price' => $this->price,
        'stock' => $this->stock,
    ];
}
```

---

## Validation — FormRequest

- Dùng FormRequest (lỗi validation trả JSON 422 tự động khi request là API).
- Không cần override `failedValidation` nếu dùng `Accept: application/json`.

---

## Authentication

- Bảo vệ route bằng middleware `auth:sanctum`.
- Tạo token: `$user->createToken('api-token')->plainTextToken`.
- Logout: `$request->user()->currentAccessToken()->delete()`.

---

## Rate Limiting

Khai báo trong `AppServiceProvider::boot()`:

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

---

## Anti-Patterns (CẤM trong API)

- ❌ Render Blade view trong API Controller
- ❌ Dùng `redirect()` trong API Controller
- ❌ Trả `$model->toArray()` trực tiếp (bỏ qua Resource)
- ❌ Expose trường nhạy cảm (password, remember_token)
- ❌ HTTP status code không đúng ngữ nghĩa
- ❌ Route API không có `auth:sanctum` khi cần bảo vệ
