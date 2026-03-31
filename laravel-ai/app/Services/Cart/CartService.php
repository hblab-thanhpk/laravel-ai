<?php

namespace App\Services\Cart;

use App\DTOs\Cart\AddCartItemData;
use App\DTOs\Cart\UpdateCartItemData;
use App\Exceptions\Cart\InsufficientStockException;
use App\Exceptions\Cart\ProductInactiveException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        /** @var Cart */
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function getCartWithItems(User $user): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $cart->load(['items.product.category', 'items.variant']);

        return $cart;
    }

    public function addItem(User $user, AddCartItemData $data): Cart
    {
        $product = Product::findOrFail($data->productId);

        if (! $product->is_active) {
            throw new ProductInactiveException('Sản phẩm không còn hoạt động.');
        }

        $variant = null;
        if ($data->variantId !== null) {
            $variant = ProductVariant::findOrFail($data->variantId);

            if (! $variant->is_active) {
                throw new ProductInactiveException('Biến thể sản phẩm không còn hoạt động.');
            }
        }

        DB::beginTransaction();
        try {
            $cart = $this->getOrCreateCart($user);

            // Kiểm tra item đã tồn tại (cùng product + variant)
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $data->productId)
                ->where('variant_id', $data->variantId)
                ->first();

            if ($existingItem !== null) {
                $newQuantity = $existingItem->quantity + $data->quantity;
                $this->validateStock($product, $variant, $newQuantity);
                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                $this->validateStock($product, $variant, $data->quantity);
                $cart->items()->create([
                    'product_id' => $data->productId,
                    'variant_id' => $data->variantId,
                    'quantity'   => $data->quantity,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->getCartWithItems($user);
    }

    public function updateItem(User $user, CartItem $item, UpdateCartItemData $data): Cart
    {
        $this->authorizeItemOwnership($user, $item);

        $item->load(['product', 'variant']);
        $this->validateStock($item->product, $item->variant, $data->quantity);

        $item->update(['quantity' => $data->quantity]);

        return $this->getCartWithItems($user);
    }

    public function removeItem(User $user, CartItem $item): Cart
    {
        $this->authorizeItemOwnership($user, $item);
        $item->delete();

        return $this->getCartWithItems($user);
    }

    public function clearCart(User $user): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->delete();

        $cart->load('items');

        return $cart;
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function authorizeItemOwnership(User $user, CartItem $item): void
    {
        $cart = $this->getOrCreateCart($user);

        if ($item->cart_id !== $cart->id) {
            abort(403, 'Bạn không có quyền thao tác item này.');
        }
    }

    private function validateStock(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $available = $variant !== null ? $variant->stock : $product->stock;

        if ($quantity > $available) {
            throw new InsufficientStockException(
                "Số lượng yêu cầu ({$quantity}) vượt quá tồn kho ({$available})."
            );
        }
    }
}
