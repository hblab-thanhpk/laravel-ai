<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_admin_can_view_user_management_screens(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.users.index')
            ->assertSee($user->email);

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertViewIs('admin.users.create');

        $this->actingAs($admin)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertViewIs('admin.users.show')
            ->assertSee($user->email);

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertViewIs('admin.users.edit')
            ->assertSee($user->email);
    }

    public function test_guest_cannot_access_user_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->get(route('admin.users.index'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.users.create'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.users.show', $user))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.users.edit', $user))->assertRedirect(route('admin.login.form'));
    }

    public function test_authenticated_admin_can_create_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $staffRoleId = Role::query()->where('name', 'staff')->value('id');

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'new.user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $staffRoleId,
            ]);

        $createdUser = User::query()->where('email', 'new.user@example.com')->first();

        $this->assertNotNull($createdUser);

        $response->assertRedirect(route('admin.users.show', $createdUser));
        $this->assertDatabaseHas('users', [
            'email' => 'new.user@example.com',
            'name' => 'New User',
            'role_id' => $staffRoleId,
        ]);
    }

    public function test_authenticated_admin_can_update_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated User',
                'email' => 'updated.user@example.com',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
                'role_id' => $adminRoleId,
            ]);

        $response->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated User',
            'email' => 'updated.user@example.com',
            'role_id' => $adminRoleId,
        ]);
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_authenticated_admin_can_delete_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        /** @var User $member */
        $member = User::factory()->create();

        /** @var User $targetUser */
        $targetUser = User::factory()->create();

        $this->actingAs($member)
            ->get(route('admin.users.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->get(route('admin.users.show', $targetUser))
            ->assertRedirect('/')
            ->assertSessionHas('error');
    }

    public function test_non_admin_cannot_write_user_management(): void
    {
        /** @var User $member */
        $member = User::factory()->create();

        /** @var User $targetUser */
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target.user@example.com',
        ]);

        $this->actingAs($member)
            ->post(route('admin.users.store'), [
                'name' => 'Forbidden Create',
                'email' => 'forbidden.create@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->put(route('admin.users.update', $targetUser), [
                'name' => 'Forbidden Update',
                'email' => 'forbidden.update@example.com',
            ])
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->delete(route('admin.users.destroy', $targetUser))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', [
            'email' => 'forbidden.create@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Target User',
            'email' => 'target.user@example.com',
        ]);
    }

    public function test_admin_cannot_demote_last_remaining_admin(): void
    {
        Log::spy();

        /** @var User $lastAdmin */
        $lastAdmin = User::factory()->admin()->create([
            'name' => 'Last Admin',
            'email' => 'last.admin@example.com',
        ]);

        $customerRoleId = Role::query()->where('name', 'customer')->value('id');

        $response = $this->actingAs($lastAdmin)
            ->from(route('admin.users.edit', $lastAdmin))
            ->put(route('admin.users.update', $lastAdmin), [
                'name' => 'Last Admin',
                'email' => 'last.admin@example.com',
                'role_id' => $customerRoleId,
            ]);

        $response
            ->assertRedirect(route('admin.users.edit', $lastAdmin))
            ->assertSessionHas('error');

        $adminRoleId = Role::query()->where('name', 'admin')->value('id');
        $this->assertDatabaseHas('users', [
            'id' => $lastAdmin->id,
            'role_id' => $adminRoleId,
        ]);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use ($lastAdmin): bool {
                return $message === 'Blocked attempt to demote last admin.'
                    && ($context['event'] ?? null) === 'admin.last_admin_protection'
                    && ($context['action'] ?? null) === 'demote_last_admin'
                    && ($context['actor_id'] ?? null) === $lastAdmin->id
                    && ($context['target_user_id'] ?? null) === $lastAdmin->id;
            });
    }

    public function test_admin_cannot_delete_last_remaining_admin(): void
    {
        Log::spy();

        /** @var User $lastAdmin */
        $lastAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($lastAdmin)
            ->delete(route('admin.users.destroy', $lastAdmin));

        $response
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $adminRoleId = Role::query()->where('name', 'admin')->value('id');
        $this->assertDatabaseHas('users', [
            'id' => $lastAdmin->id,
            'role_id' => $adminRoleId,
        ]);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use ($lastAdmin): bool {
                return $message === 'Blocked attempt to delete last admin.'
                    && ($context['event'] ?? null) === 'admin.last_admin_protection'
                    && ($context['action'] ?? null) === 'delete_last_admin'
                    && ($context['actor_id'] ?? null) === $lastAdmin->id
                    && ($context['target_user_id'] ?? null) === $lastAdmin->id;
            });
    }

    public function test_edit_form_disables_role_select_for_last_admin(): void
    {
        /** @var User $lastAdmin */
        $lastAdmin = User::factory()->admin()->create([
            'email' => 'last.admin@example.com',
        ]);

        $response = $this->actingAs($lastAdmin)
            ->get(route('admin.users.edit', $lastAdmin));

        $response
            ->assertOk()
            ->assertSee('id="role_id"', false)
            ->assertSee('disabled', false)
            ->assertSee('Không thể thay đổi role vì đây là admin cuối cùng trong hệ thống.');
    }

    public function test_authenticated_admin_can_use_advanced_filters_on_users_index(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create([
            'email' => 'owner.admin@example.com',
            'name' => 'Owner Admin',
        ]);

        $customerRoleId = Role::query()->where('name', 'customer')->value('id');

        User::factory()->create([
            'name' => 'Zed Member',
            'email' => 'zed.member@example.com',
            'role_id' => $customerRoleId,
        ]);

        User::factory()->admin()->create([
            'name' => 'Alpha Admin',
            'email' => 'alpha.admin@example.com',
        ]);

        $adminRoleId = Role::query()->where('name', 'admin')->value('id');

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'search' => 'admin',
                'role_id' => $adminRoleId,
                'sort_by' => 'name',
                'sort_dir' => 'asc',
                'per_page' => '10',
            ]));

        $response
            ->assertOk()
            ->assertSee('alpha.admin@example.com')
            ->assertSee('owner.admin@example.com')
            ->assertDontSee('zed.member@example.com');
    }
}