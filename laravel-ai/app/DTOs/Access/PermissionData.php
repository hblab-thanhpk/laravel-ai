<?php

namespace App\DTOs\Access;

final readonly class PermissionData
{
    public function __construct(
        public string $name,
        public string $displayName,
        public ?string $description,
    ) {}

    /**
     * @param  array{name: string, display_name: string, description?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            displayName: trim($data['display_name']),
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
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
