<?php

namespace App\DTOs\Catalog;

final readonly class CategoryData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $isActive,
        public ?string $parentId,
    ) {}

    /**
     * @param  array{name: string, slug: string, description?: string|null, is_active: bool, parent_id?: string|null}  $data
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
            parentId: isset($data['parent_id']) && (string) $data['parent_id'] !== ''
                ? (string) $data['parent_id']
                : null,
        );
    }

    /**
     * @return array{name: string, slug: string, description: string|null, is_active: bool, parent_id: string|null}
     */
    public function toPayload(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'parent_id' => $this->parentId,
        ];
    }
}
