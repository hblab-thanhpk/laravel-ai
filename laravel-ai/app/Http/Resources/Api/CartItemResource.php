<?php

namespace App\Http\Resources\Api;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CartItem
 */
class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'quantity'   => $this->quantity,
            'product'    => $this->whenLoaded('product', fn () => [
                'id'       => $this->product->id,
                'name'     => $this->product->name,
                'slug'     => $this->product->slug,
                'price'    => $this->product->price,
                'is_active' => $this->product->is_active,
                'category' => $this->product->relationLoaded('category') ? [
                    'id'   => $this->product->category->id,
                    'name' => $this->product->category->name,
                ] : null,
            ]),
            'variant'    => $this->whenLoaded('variant', fn () => $this->variant !== null ? [
                'id'       => $this->variant->id,
                'sku'      => $this->variant->sku,
                'size'     => $this->variant->size,
                'color'    => $this->variant->color,
                'price'    => $this->variant->price,
                'stock'    => $this->variant->stock,
                'is_active' => $this->variant->is_active,
            ] : null),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
