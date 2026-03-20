<?php

namespace App\Services\Access;

use App\DTOs\Access\PermissionData;
use App\DTOs\Access\PermissionQueryData;
use App\Exceptions\CannotDeletePermissionException;
use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function paginate(PermissionQueryData $queryData): LengthAwarePaginator
    {
        $query = Permission::query()->withCount('roles');

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$normalizedSearch}%"]);
            });
        }

        $query
            ->orderBy($queryData->sortBy, $queryData->sortDirection)
            ->orderBy('id');

        return $query
            ->paginate($queryData->perPage)
            ->withQueryString();
    }

    public function create(PermissionData $permissionData): Permission
    {
        return DB::transaction(function () use ($permissionData): Permission {
            $permission = Permission::query()->create([
                ...$permissionData->toPayload(),
                'is_system' => false,
            ]);

            Cache::forget('permissions:all');

            return $permission;
        });
    }

    public function update(Permission $permission, PermissionData $permissionData): Permission
    {
        return DB::transaction(function () use ($permission, $permissionData): Permission {
            $payload = $permissionData->toPayload();

            if ($permission->is_system) {
                unset($payload['name']);
            }

            $permission->fill($payload);
            $permission->save();

            Cache::forget('permissions:all');

            return $permission->refresh();
        });
    }

    public function delete(Permission $permission): void
    {
        DB::transaction(function () use ($permission): void {
            if ($permission->is_system) {
                throw CannotDeletePermissionException::systemPermission($permission->display_name);
            }

            if ($permission->roles()->exists()) {
                throw CannotDeletePermissionException::permissionInUse($permission->display_name);
            }

            $permission->delete();

            Cache::forget('permissions:all');
        });
    }
}
