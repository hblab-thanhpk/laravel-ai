<?php

namespace App\Services\Auth;

use App\DTOs\Auth\LoginData;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @return array<string, mixed>|null
     */
    public function login(LoginData $loginData): ?array
    {
        $user = User::query()
            ->where('email', $loginData->email)
            ->first();

        if ($user === null || ! Hash::check($loginData->password, $user->password)) {
            return null;
        }

        $plainTextToken = $user
            ->createToken($loginData->deviceName)
            ->plainTextToken;

        return [
            'access_token' => $plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    public function logout(User $user): void
    {
        /** @var PersonalAccessToken|null $currentAccessToken */
        $currentAccessToken = $user->currentAccessToken();

        if ($currentAccessToken !== null) {
            $user->tokens()->whereKey($currentAccessToken->getKey())->delete();

            return;
        }

        $user->tokens()->delete();
    }
}