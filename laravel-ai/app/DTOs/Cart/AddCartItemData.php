<?php

namespace App\DTOs\Cart;

final readonly class AddCartItemData
{
    public function __construct(
        public string $productId,
        public ?string $variantId,
        public int $quantity,
    ) {}

    /**
     * @param array{product_id: string, variant_id?: string|null, quantity: int} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            variantId: isset($data['variant_id']) && trim((string) $data['variant_id']) !== ''
                ? (string) $data['variant_id']
                : null,
            quantity: (int) $data['quantity'],
        );
    }
}
