<?php

namespace App\Services\User;

use App\DTOs\User\UserData;
use App\DTOs\User\UserQueryData;
use App\Exceptions\CannotModifyLastAdminException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function paginate(UserQueryData $queryData): LengthAwarePaginator
    {
        $query = User::query();

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$normalizedSearch}%"]);
            });
        }

        if ($queryData->roleId !== null) {
            $query->where('role_id', $queryData->roleId);
        }

        $query
            ->orderBy($queryData->sortBy, $queryData->sortDirection)
            ->orderBy('id');

        return $query
            ->paginate($queryData->perPage)
            ->withQueryString();
    }

    public function create(UserData $userData): User
    {
        return DB::transaction(function () use ($userData): User {
            return User::query()->create($userData->toCreatePayload());
        });
    }

    public function update(User $user, UserData $userData): User
    {
        return DB::transaction(function () use ($user, $userData): User {
            $this->guardLastAdminBeforeUpdate($user, $userData->roleId);

            $user->fill($userData->toUpdatePayload());
            $user->save();
            $user->flushPermissionsCache();

            return $user->refresh();
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->guardLastAdminBeforeDelete($user);

            $user->flushPermissionsCache();
            $user->delete();
        });
    }

    public function isLastAdmin(User $user): bool
    {
        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        if ($adminRoleId === null || $user->role_id !== $adminRoleId) {
            return false;
        }

        return ! User::query()
            ->where('role_id', $adminRoleId)
            ->whereKeyNot($user->getKey())
            ->exists();
    }

    private function guardLastAdminBeforeUpdate(User $user, ?string $nextRoleId): void
    {
        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        if ($adminRoleId === null || $user->role_id !== $adminRoleId || $nextRoleId === $adminRoleId) {
            return;
        }

        $hasAnotherAdmin = User::query()
            ->where('role_id', $adminRoleId)
            ->whereKeyNot($user->getKey())
            ->lockForUpdate()
            ->exists();

        if (! $hasAnotherAdmin) {
            throw CannotModifyLastAdminException::whenDemoting();
        }
    }

    private function guardLastAdminBeforeDelete(User $user): void
    {
        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        if ($adminRoleId === null || $user->role_id !== $adminRoleId) {
            return;
        }

        $hasAnotherAdmin = User::query()
            ->where('role_id', $adminRoleId)
            ->whereKeyNot($user->getKey())
            ->lockForUpdate()
            ->exists();

        if (! $hasAnotherAdmin) {
            throw CannotModifyLastAdminException::whenDeleting();
        }
    }
}
