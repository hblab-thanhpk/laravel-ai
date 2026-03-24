<?php

namespace App\DTOs\Access;

final readonly class RoleData
{
    /**
     * @param  array<int, string>  $permissionIds
     */
    public function __construct(
        public string $name,
        public string $displayName,
        public ?string $description,
        public array $permissionIds,
    ) {}

    /**
     * @param  array{name: string, display_name: string, description?: string|null, permission_ids?: array<int, string>}  $data
     */
    public static function fromArray(array $data): self
    {
        $permissionIds = array_values(array_unique($data['permission_ids'] ?? []));

        return new self(
            name: trim($data['name']),
            displayName: trim($data['display_name']),
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
            permissionIds: $permissionIds,
        );
    }

    /**
     * @return array{name: string, display_name: string, description: string|null}
     */
    public function toPayload(): array
    {
        return [
            'name' => $this->name,
            'display_name' => $this->displayName,
            'description' => $this->description,
        ];
    }
}
