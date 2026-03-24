<?php

namespace App\DTOs\User;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public ?string $roleId,
    ) {}

    /**
     * @param  array{name: string, email: string, password?: string|null, role_id?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        $roleId = isset($data['role_id']) && (string) $data['role_id'] !== '' ? (string) $data['role_id'] : null;

        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            roleId: $roleId,
        );
    }

    /**
     * @return array{name: string, email: string, role_id: string|null, password?: string|null}
     */
    public function toCreatePayload(): array
    {
        return $this->toUpdatePayload();
    }

    /**
     * @return array{name: string, email: string, role_id: string|null, password?: string|null}
     */
    public function toUpdatePayload(): array
    {
        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->roleId,
        ];

        if ($this->password !== null) {
            $payload['password'] = $this->password;
        }

        return $payload;
    }
}
