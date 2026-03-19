<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\User\UserData;
use App\DTOs\User\UserQueryData;
use App\Exceptions\CannotModifyLastAdminException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexUserRequest;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Access\RoleService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(IndexUserRequest $request, UserService $userService, RoleService $roleService): View
    {
        $queryData = UserQueryData::fromArray($request->validated());

        return view('admin.users.index', [
            'users' => $userService->paginate($queryData),
            'filters' => $queryData->toArray(),
            'roles' => $roleService->allForForm(),
        ]);
    }

    public function create(RoleService $roleService): View
    {
        return view('admin.users.create', [
            'roles' => $roleService->allForForm(),
        ]);
    }

    public function store(StoreUserRequest $request, UserService $userService): RedirectResponse
    {
        $user = $userService->create(UserData::fromArray($request->validated()));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Tạo người dùng thành công.');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user, UserService $userService, RoleService $roleService): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roleService->allForForm(),
            'isLastAdmin' => $userService->isLastAdmin($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UserService $userService): RedirectResponse
    {
        try {
            $user = $userService->update($user, UserData::fromArray($request->validated()));
        } catch (CannotModifyLastAdminException $exception) {
            Log::warning('Blocked attempt to demote last admin.', [
                'event' => 'admin.last_admin_protection',
                'action' => 'demote_last_admin',
                'actor_id' => Auth::id(),
                'actor_email' => (string) data_get(Auth::user(), 'email'),
                'target_user_id' => (string) $user->id,
                'target_user_email' => (string) $user->email,
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user, UserService $userService): RedirectResponse
    {
        try {
            $userService->delete($user);
        } catch (CannotModifyLastAdminException $exception) {
            Log::warning('Blocked attempt to delete last admin.', [
                'event' => 'admin.last_admin_protection',
                'action' => 'delete_last_admin',
                'actor_id' => Auth::id(),
                'actor_email' => (string) data_get(Auth::user(), 'email'),
                'target_user_id' => (string) $user->id,
                'target_user_email' => (string) $user->email,
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công.');
    }
}