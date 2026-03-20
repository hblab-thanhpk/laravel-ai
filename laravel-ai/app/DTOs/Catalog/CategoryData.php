<?php

namespace App\DTOs\Catalog;

final readonly class CategoryData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $isActive,
    ) {
    }

    /**
     * @param  array{name: string, slug: string, description?: string|null, is_active: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            slug: trim($data['slug']),
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
            isActive: (bool) $data['is_active'],
        );
    }

    /**
     * @return array{name: string, slug: string, description: string|null, is_active: bool}
     */
    public function toPayload(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
