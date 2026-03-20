<?php

namespace App\DTOs\Catalog;

final readonly class ProductData
{
    public function __construct(
        public ?string $categoryId,
        public string $name,
        public string $slug,
        public string $sku,
        public string $price,
        public int $stock,
        public ?string $description,
        public bool $isActive,
    ) {
    }

    /**
     * @param  array{category_id?: string|null, name: string, slug: string, sku: string, price: numeric-string|int|float, stock: int, description?: string|null, is_active: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: isset($data['category_id']) && trim((string) $data['category_id']) !== ''
                ? (string) $data['category_id']
                : null,
            name: trim($data['name']),
            slug: trim($data['slug']),
            sku: trim((string) $data['sku']),
            price: (string) $data['price'],
            stock: (int) $data['stock'],
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
            isActive: (bool) $data['is_active'],
        );
    }

    /**
     * @return array{category_id: string|null, name: string, slug: string, sku: string, price: string, stock: int, description: string|null, is_active: bool}
     */
    public function toPayload(): array
    {
        return [
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
