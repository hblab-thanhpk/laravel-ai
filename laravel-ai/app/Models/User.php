<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Role, self>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        $role = $this->relationLoaded('role')
            ? $this->getRelation('role')
            : $this->role()->first();

        if ($role === null) {
            return false;
        }

        return $role->name === $roleName;
    }

    public function hasPermission(string $permissionName): bool
    {
        return in_array($permissionName, $this->getCachedPermissions(), true);
    }

    public function canAccessAdminPanel(): bool
    {
        if ($this->role_id === null) {
            return false;
        }

        return count($this->getCachedPermissions()) > 0;
    }

    /**
     * @return array<int, string>
     */
    private function getCachedPermissions(): array
    {
        return Cache::remember(
            "user:{$this->id}:permissions",
            now()->addMinutes(10),
            function (): array {
                $role = $this->role()->with('permissions')->first();

                return $role?->permissions->pluck('name')->all() ?? [];
            },
        );
    }

    public function flushPermissionsCache(): void
    {
        Cache::forget("user:{$this->id}:permissions");
    }
}
