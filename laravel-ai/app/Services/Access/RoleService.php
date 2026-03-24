<?php

namespace App\Services\Access;

use App\DTOs\Access\RoleData;
use App\DTOs\Access\RoleQueryData;
use App\Exceptions\CannotDeleteRoleException;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
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
     * @return Collection<int, Role>
     */
    public function allForForm(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            'roles:all',
            now()->addHour(),
            static fn (): array => Role::query()
                ->orderBy('display_name')
                ->get(['id', 'name', 'display_name'])
                ->toArray(),
        );

        return Role::hydrate($rows);
    }

    /**
     * @return Collection<int, Permission>
     */
    public function allPermissionsForForm(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            'permissions:all',
            now()->addHour(),
            static fn (): array => Permission::query()
                ->orderBy('display_name')
                ->get(['id', 'name', 'display_name'])
                ->toArray(),
        );

        return Permission::hydrate($rows);
    }

    public function create(RoleData $roleData): Role
    {
        $role = DB::transaction(function () use ($roleData): Role {
            $role = Role::query()->create([
                ...$roleData->toPayload(),
                'is_system' => false,
            ]);

            $role->permissions()->sync($roleData->permissionIds);

            return $role->refresh();
        });

        Cache::forget('roles:all');

        return $role;
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

            // Xóa cache permissions của tất cả users thuộc role này
            User::query()
                ->where('role_id', $role->id)
                ->pluck('id')
                ->each(static fn (string $userId): bool => Cache::forget("user:{$userId}:permissions"));

            return $role->refresh();
        });

        Cache::forget('roles:all');

        return $role;
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

        Cache::forget('roles:all');
    }
}
