<?php

namespace App\Http\Controllers\Admin\Access;

use App\DTOs\Access\RoleData;
use App\DTOs\Access\RoleQueryData;
use App\Exceptions\CannotDeleteRoleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\IndexRoleRequest;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\Access\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(IndexRoleRequest $request, RoleService $roleService): View
    {
        $queryData = RoleQueryData::fromArray($request->validated());

        return view('admin.roles.index', [
            'roles' => $roleService->paginate($queryData),
            'filters' => $queryData->toArray(),
        ]);
    }

    public function create(RoleService $roleService): View
    {
        return view('admin.roles.create', [
            'permissions' => $roleService->allPermissionsForForm(),
        ]);
    }

    public function store(StoreRoleRequest $request, RoleService $roleService): RedirectResponse
    {
        $role = $roleService->create(RoleData::fromArray($request->validated()));

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Tạo role thành công.');
    }

    public function show(Role $role): View
    {
        return view('admin.roles.show', [
            'role' => $role->load('permissions')->loadCount('users'),
        ]);
    }

    public function edit(Role $role, RoleService $roleService): View
    {
        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $roleService->allPermissionsForForm(),
            'isSystemRole' => $role->is_system,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role, RoleService $roleService): RedirectResponse
    {
        $role = $roleService->update($role, RoleData::fromArray($request->validated()));

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'Cập nhật role thành công.');
    }

    public function destroy(Role $role, RoleService $roleService): RedirectResponse
    {
        try {
            $roleService->delete($role);
        } catch (CannotDeleteRoleException $exception) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Xóa role thành công.');
    }
}
