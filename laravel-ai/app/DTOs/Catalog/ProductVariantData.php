<?php

namespace App\DTOs\Catalog;

final readonly class ProductVariantData
{
    public function __construct(
        public string $sku,
        public ?string $size,
        public ?string $color,
        public ?string $price,
        public int $stock,
        public bool $isActive,
    ) {}

    /**
     * @param  array{sku: string, size?: string|null, color?: string|null, price?: numeric-string|int|float|null, stock: int, is_active: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sku: trim((string) $data['sku']),
            size: isset($data['size']) && trim((string) $data['size']) !== '' ? trim((string) $data['size']) : null,
            color: isset($data['color']) && trim((string) $data['color']) !== '' ? trim((string) $data['color']) : null,
            price: isset($data['price']) && trim((string) $data['price']) !== '' ? (string) $data['price'] : null,
            stock: (int) $data['stock'],
            isActive: (bool) $data['is_active'],
        );
    }

    /**
     * @return array{sku: string, size: string|null, color: string|null, price: string|null, stock: int, is_active: bool}
     */
    public function toPayload(): array
    {
        return [
            'sku' => $this->sku,
            'size' => $this->size,
            'color' => $this->color,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_active' => $this->isActive,
        ];
    }
}
