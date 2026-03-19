<?php

namespace App\Services\Access;

use App\DTOs\Access\RoleData;
use App\DTOs\Access\RoleQueryData;
use App\Exceptions\CannotDeleteRoleException;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function paginate(RoleQueryData $queryData): LengthAwarePaginator
    {
        $query = Role::query()->withCount(['users', 'permissions']);

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

    /**
     * @return Collection<int, Permission>
     */
    public function allPermissionsForForm(): Collection
    {
        return Permission::query()
            ->orderBy('display_name')
            ->get();
    }

    public function create(RoleData $roleData): Role
    {
        return DB::transaction(function () use ($roleData): Role {
            $role = Role::query()->create([
                ...$roleData->toPayload(),
                'is_system' => false,
            ]);

            $role->permissions()->sync($roleData->permissionIds);

            return $role->refresh();
        });
    }

    public function update(Role $role, RoleData $roleData): Role
    {
        return DB::transaction(function () use ($role, $roleData): Role {
            $payload = $roleData->toPayload();

            if ($role->is_system) {
                unset($payload['name']);
            }

            $role->fill($payload);
            $role->save();
            $role->permissions()->sync($roleData->permissionIds);

            return $role->refresh();
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            if ($role->is_system) {
                throw CannotDeleteRoleException::systemRole($role->display_name);
            }

            if ($role->users()->exists()) {
                throw CannotDeleteRoleException::roleInUse($role->display_name);
            }

            $role->permissions()->detach();
            $role->delete();
        });
    }
}
