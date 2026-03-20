<?php

namespace App\DTOs\Catalog;

final readonly class CategoryQueryData
{
    public const SORTABLE_COLUMNS = [
        'name',
        'slug',
        'created_at',
        'updated_at',
    ];

    public const SORT_DIRECTIONS = ['asc', 'desc'];

    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function __construct(
        public ?string $search,
        public ?bool $isActive,
        public int $perPage,
        public string $sortBy,
        public string $sortDirection,
    ) {
    }

    /**
     * @param  array{search?: string|null, status?: string|null, per_page?: int|string|null, sort_by?: string|null, sort_dir?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        $search = isset($data['search']) ? trim((string) $data['search']) : '';
        $status = strtolower((string) ($data['status'] ?? 'all'));
        $perPage = (int) ($data['per_page'] ?? 10);
        $sortBy = (string) ($data['sort_by'] ?? 'created_at');
        $sortDirection = strtolower((string) ($data['sort_dir'] ?? 'desc'));

        return new self(
            search: $search === '' ? null : $search,
            isActive: match ($status) {
                'active' => true,
                'inactive' => false,
                default => null,
            },
            perPage: in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 10,
            sortBy: in_array($sortBy, self::SORTABLE_COLUMNS, true) ? $sortBy : 'created_at',
            sortDirection: in_array($sortDirection, self::SORT_DIRECTIONS, true) ? $sortDirection : 'desc',
        );
    }

    /**
     * @return array{search: string, status: string, per_page: int, sort_by: string, sort_dir: string}
     */
    public function toArray(): array
    {
        $status = match ($this->isActive) {
            true => 'active',
            false => 'inactive',
            default => 'all',
        };

        return [
            'search' => $this->search ?? '',
            'status' => $status,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDirection,
        ];
    }
}
