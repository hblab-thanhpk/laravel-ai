<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    // throttle:api (group default) bị exclude — dùng api-login chuyên biệt (per-email, 10/min)
    Route::post('/login', [AuthController::class, 'login'])
        ->withoutMiddleware('throttle:api')
        ->middleware('throttle:api-login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('products', ProductController::class);

    // Cart
    Route::prefix('cart')->group(function (): void {
        Route::get('/',               [CartController::class, 'index']);
        Route::post('/items',         [CartController::class, 'addItem']);
        Route::patch('/items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
        Route::delete('/',            [CartController::class, 'clear']);
    });
});
