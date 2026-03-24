<?php

namespace App\DTOs\Auth;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName,
    ) {}

    /**
     * @param  array{email: string, password: string, device_name: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            deviceName: $data['device_name'],
        );
    }
}
