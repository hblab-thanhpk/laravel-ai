<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Auth\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $loginData = LoginData::fromArray([
            ...$request->validated(),
            'device_name' => $request->validated('device_name')
                ?? $request->userAgent()
                ?? 'api-token',
        ]);

        $authPayload = $authService->login($loginData);

        if ($authPayload === null) {
            return $this->errorResponse(
                'Thông tin đăng nhập không chính xác.',
                [
                    'email' => ['Thông tin đăng nhập không chính xác.'],
                ],
                401,
            );
        }

        return $this->successResponse('Đăng nhập thành công.', $authPayload);
    }

    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $authService->logout($user);

        return $this->successResponse('Đăng xuất thành công.');
    }
}
