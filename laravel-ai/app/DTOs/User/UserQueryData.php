<?php

namespace App\DTOs\User;

final readonly class UserQueryData
{
    public const SORTABLE_COLUMNS = [
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    public const SORT_DIRECTIONS = ['asc', 'desc'];

    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function __construct(
        public ?string $search,
        public ?string $roleId,
        public int $perPage,
        public string $sortBy,
        public string $sortDirection,
    ) {
    }

    /**
     * @param  array{search?: string|null, role_id?: string|null, per_page?: int|string|null, sort_by?: string|null, sort_dir?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        $search = isset($data['search']) ? trim((string) $data['search']) : '';
        $sortBy = (string) ($data['sort_by'] ?? 'created_at');
        $sortDirection = strtolower((string) ($data['sort_dir'] ?? 'desc'));
        $perPage = (int) ($data['per_page'] ?? 10);
        $roleId = isset($data['role_id']) && (string) $data['role_id'] !== '' && $data['role_id'] !== 'all'
            ? (string) $data['role_id']
            : null;

        return new self(
            search: $search === '' ? null : $search,
            roleId: $roleId,
            perPage: in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 10,
            sortBy: in_array($sortBy, self::SORTABLE_COLUMNS, true) ? $sortBy : 'created_at',
            sortDirection: in_array($sortDirection, self::SORT_DIRECTIONS, true) ? $sortDirection : 'desc',
        );
    }

    /**
     * @return array{search: string, role_id: string, per_page: int, sort_by: string, sort_dir: string}
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search ?? '',
            'role_id' => $this->roleId ?? 'all',
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDirection,
        ];
    }
}