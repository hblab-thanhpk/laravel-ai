<?php

namespace App\Http\Controllers\Admin\Access;

use App\DTOs\Access\PermissionData;
use App\DTOs\Access\PermissionQueryData;
use App\Exceptions\CannotDeletePermissionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\IndexPermissionRequest;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\Access\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(IndexPermissionRequest $request, PermissionService $permissionService): View
    {
        $queryData = PermissionQueryData::fromArray($request->validated());

        return view('admin.permissions.index', [
            'permissions' => $permissionService->paginate($queryData),
            'filters' => $queryData->toArray(),
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request, PermissionService $permissionService): RedirectResponse
    {
        $permission = $permissionService->create(PermissionData::fromArray($request->validated()));

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'Tạo permission thành công.');
    }

    public function show(Permission $permission): View
    {
        return view('admin.permissions.show', [
            'permission' => $permission
                ->load('roles')
                ->loadCount('roles'),
        ]);
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.edit', [
            'permission' => $permission,
            'isSystemPermission' => $permission->is_system,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission, PermissionService $permissionService): RedirectResponse
    {
        $permission = $permissionService->update($permission, PermissionData::fromArray($request->validated()));

        return redirect()
            ->route('admin.permissions.show', $permission)
            ->with('success', 'Cập nhật permission thành công.');
    }

    public function destroy(Permission $permission, PermissionService $permissionService): RedirectResponse
    {
        try {
            $permissionService->delete($permission);
        } catch (CannotDeletePermissionException $exception) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Xóa permission thành công.');
    }
}
