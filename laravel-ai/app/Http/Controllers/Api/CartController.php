<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Cart\AddCartItemData;
use App\DTOs\Cart\UpdateCartItemData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\AddCartItemRequest;
use App\Http\Requests\Api\Cart\UpdateCartItemRequest;
use App\Http\Resources\Api\CartResource;
use App\Models\CartItem;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems($request->user());

        return $this->successResponse(
            'Giỏ hàng của bạn.',
            (new CartResource($cart))->resolve(),
        );
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->addItem(
            $request->user(),
            AddCartItemData::fromArray($request->validated()),
        );

        return $this->successResponse(
            'Thêm sản phẩm vào giỏ hàng thành công.',
            (new CartResource($cart))->resolve(),
            201,
        );
    }

    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->cartService->updateItem(
            $request->user(),
            $cartItem,
            UpdateCartItemData::fromArray($request->validated()),
        );

        return $this->successResponse(
            'Cập nhật giỏ hàng thành công.',
            (new CartResource($cart))->resolve(),
        );
    }

    public function removeItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->cartService->removeItem($request->user(), $cartItem);

        return $this->successResponse(
            'Đã xoá sản phẩm khỏi giỏ hàng.',
            (new CartResource($cart))->resolve(),
        );
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cartService->clearCart($request->user());

        return $this->successResponse(
            'Đã xoá toàn bộ giỏ hàng.',
            (new CartResource($cart))->resolve(),
        );
    }
}
