<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_role_permission_seeder_syncs_new_catalog_permissions_to_staff_role(): void
    {
        $expectedCatalogPermissions = [
            'categories_view',
            'categories_create',
            'categories_update',
            'categories_delete',
            'products_view',
            'products_create',
            'products_update',
            'products_delete',
            'product_variants_view',
            'product_variants_create',
            'product_variants_update',
            'product_variants_delete',
        ];

        foreach ($expectedCatalogPermissions as $permissionName) {
            $this->assertDatabaseHas('permissions', [
                'name' => $permissionName,
                'is_system' => true,
            ]);
        }

        $staffRole = Role::query()->where('name', 'staff')->firstOrFail();
        $staffPermissionNames = $staffRole->permissions()->pluck('permissions.name')->all();

        $this->assertEqualsCanonicalizing(
            [
                ...$expectedCatalogPermissions,
                'manage_products',
                'manage_inventory',
                'manage_orders',
            ],
            $staffPermissionNames,
        );
    }

    public function test_authenticated_admin_can_view_role_and_permission_management_screens(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $permission = Permission::query()->create([
            'name' => 'manage_catalog',
            'display_name' => 'Quản lý catalog',
            'description' => 'Quyền quản lý catalog.',
            'is_system' => false,
        ]);

        $role = Role::query()->create([
            'name' => 'catalog_manager',
            'display_name' => 'Catalog Manager',
            'description' => 'Vai trò quản lý catalog.',
            'is_system' => false,
        ]);

        $role->permissions()->sync([$permission->id]);

        $this->actingAs($admin)
            ->get(route('admin.roles.index', [
                'search' => 'catalog_manager',
            ]))
            ->assertOk()
            ->assertViewIs('admin.roles.index')
            ->assertSee($role->display_name);

        $this->actingAs($admin)
            ->get(route('admin.roles.create'))
            ->assertOk()
            ->assertViewIs('admin.roles.create');

        $this->actingAs($admin)
            ->get(route('admin.roles.show', $role))
            ->assertOk()
            ->assertViewIs('admin.roles.show')
            ->assertSee($role->display_name);

        $this->actingAs($admin)
            ->get(route('admin.roles.edit', $role))
            ->assertOk()
            ->assertViewIs('admin.roles.edit')
            ->assertSee($role->display_name);

        $this->actingAs($admin)
            ->get(route('admin.permissions.index', [
                'search' => 'manage_catalog',
            ]))
            ->assertOk()
            ->assertViewIs('admin.permissions.index')
            ->assertSee($permission->display_name);

        $this->actingAs($admin)
            ->get(route('admin.permissions.create'))
            ->assertOk()
            ->assertViewIs('admin.permissions.create');

        $this->actingAs($admin)
            ->get(route('admin.permissions.show', $permission))
            ->assertOk()
            ->assertViewIs('admin.permissions.show')
            ->assertSee($permission->display_name);

        $this->actingAs($admin)
            ->get(route('admin.permissions.edit', $permission))
            ->assertOk()
            ->assertViewIs('admin.permissions.edit')
            ->assertSee($permission->display_name);
    }

    public function test_guest_cannot_access_role_and_permission_management(): void
    {
        $permission = Permission::query()->create([
            'name' => 'manage_catalog',
            'display_name' => 'Quản lý catalog',
            'description' => 'Quyền quản lý catalog.',
            'is_system' => false,
        ]);

        $role = Role::query()->create([
            'name' => 'catalog_manager',
            'display_name' => 'Catalog Manager',
            'description' => 'Vai trò quản lý catalog.',
            'is_system' => false,
        ]);

        $this->get(route('admin.roles.index'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.roles.create'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.roles.show', $role))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.roles.edit', $role))->assertRedirect(route('admin.login.form'));

        $this->get(route('admin.permissions.index'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.permissions.create'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.permissions.show', $permission))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.permissions.edit', $permission))->assertRedirect(route('admin.login.form'));
    }

    public function test_non_admin_cannot_access_or_write_role_and_permission_management(): void
    {
        /** @var User $member */
        $member = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($member)
            ->get(route('admin.roles.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->post(route('admin.roles.store'), [
                'name' => 'forbidden_role',
                'display_name' => 'Forbidden Role',
                'description' => 'Should not be created.',
            ])
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->get(route('admin.permissions.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->post(route('admin.permissions.store'), [
                'name' => 'forbidden_permission',
                'display_name' => 'Forbidden Permission',
                'description' => 'Should not be created.',
            ])
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('roles', [
            'name' => 'forbidden_role',
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'forbidden_permission',
        ]);
    }

    public function test_authenticated_admin_can_create_update_and_delete_custom_role(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $viewPermission = Permission::query()->create([
            'name' => 'view_reports',
            'display_name' => 'Xem báo cáo',
            'description' => null,
            'is_system' => false,
        ]);

        $managePermission = Permission::query()->create([
            'name' => 'manage_reports',
            'display_name' => 'Quản lý báo cáo',
            'description' => null,
            'is_system' => false,
        ]);

        $storeResponse = $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'report_manager',
                'display_name' => 'Report Manager',
                'description' => 'Vai trò quản lý báo cáo.',
                'permission_ids' => [$viewPermission->id],
            ]);

        $role = Role::query()->where('name', 'report_manager')->first();

        $this->assertNotNull($role);
        $storeResponse->assertRedirect(route('admin.roles.show', $role));

        $this->assertDatabaseHas('roles', [
            'name' => 'report_manager',
            'display_name' => 'Report Manager',
            'is_system' => false,
        ]);

        $this->assertEqualsCanonicalizing(
            [$viewPermission->id],
            $role->permissions()->pluck('permissions.id')->all(),
        );

        $updateResponse = $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'report_lead',
                'display_name' => 'Report Lead',
                'description' => 'Vai trò đã cập nhật.',
                'permission_ids' => [$managePermission->id],
            ]);

        $updateResponse->assertRedirect(route('admin.roles.show', $role));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'report_lead',
            'display_name' => 'Report Lead',
        ]);

        $this->assertEqualsCanonicalizing(
            [$managePermission->id],
            $role->fresh()->permissions()->pluck('permissions.id')->all(),
        );

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_system_role_name_is_immutable_and_system_role_cannot_be_deleted(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $role = Role::query()->create([
            'name' => 'admin_system',
            'display_name' => 'Admin',
            'description' => 'System role',
            'is_system' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'hacked_admin',
                'display_name' => 'Admin Updated',
                'description' => 'Updated description',
                'permission_ids' => [],
            ])
            ->assertRedirect(route('admin.roles.show', $role));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'admin_system',
            'display_name' => 'Admin Updated',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_role_in_use_cannot_be_deleted(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $role = Role::query()->create([
            'name' => 'sales_manager',
            'display_name' => 'Sales Manager',
            'description' => null,
            'is_system' => false,
        ]);

        User::factory()->create([
            'role_id' => $role->id,
            'is_admin' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_authenticated_admin_can_create_update_and_delete_custom_permission(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $storeResponse = $this->actingAs($admin)
            ->post(route('admin.permissions.store'), [
                'name' => 'manage_coupons',
                'display_name' => 'Quản lý mã giảm giá',
                'description' => 'Quản trị coupon.',
            ]);

        $permission = Permission::query()->where('name', 'manage_coupons')->first();

        $this->assertNotNull($permission);
        $storeResponse->assertRedirect(route('admin.permissions.show', $permission));

        $this->assertDatabaseHas('permissions', [
            'name' => 'manage_coupons',
            'display_name' => 'Quản lý mã giảm giá',
            'is_system' => false,
        ]);

        $updateResponse = $this->actingAs($admin)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'manage_discounts',
                'display_name' => 'Quản lý giảm giá',
                'description' => 'Cập nhật mô tả.',
            ]);

        $updateResponse->assertRedirect(route('admin.permissions.show', $permission));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'manage_discounts',
            'display_name' => 'Quản lý giảm giá',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.permissions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_system_permission_name_is_immutable_and_system_permission_cannot_be_deleted(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $permission = Permission::query()->create([
            'name' => 'manage_users',
            'display_name' => 'Quản lý users',
            'description' => 'System permission',
            'is_system' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'hacked_permission',
                'display_name' => 'Permission Updated',
                'description' => 'Updated description',
            ])
            ->assertRedirect(route('admin.permissions.show', $permission));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'manage_users',
            'display_name' => 'Permission Updated',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.permissions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_permission_in_use_cannot_be_deleted(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $permission = Permission::query()->create([
            'name' => 'approve_orders',
            'display_name' => 'Duyệt đơn hàng',
            'description' => null,
            'is_system' => false,
        ]);

        $role = Role::query()->create([
            'name' => 'order_approver',
            'display_name' => 'Order Approver',
            'description' => null,
            'is_system' => false,
        ]);

        $role->permissions()->sync([$permission->id]);

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.permissions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
        ]);
    }

    public function test_admin_panel_user_requires_granular_permission_for_each_permission_action(): void
    {
        $role = Role::query()->create([
            'name' => 'limited_admin',
            'display_name' => 'Admin Limited',
            'description' => 'Role admin có quyền giới hạn.',
            'is_system' => false,
        ]);

        $viewPermission = Permission::query()
            ->where('name', 'permissions_view')
            ->firstOrFail();

        $targetPermission = Permission::query()->create([
            'name' => 'catalog_sync',
            'display_name' => 'Đồng bộ catalog',
            'description' => null,
            'is_system' => false,
        ]);

        $role->permissions()->sync([$viewPermission->id]);

        /** @var User $limitedAdmin */
        $limitedAdmin = User::factory()->create([
            'is_admin' => false,
            'role_id' => $role->id,
        ]);

        $this->actingAs($limitedAdmin)
            ->get(route('admin.permissions.index'))
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->get(route('admin.permissions.show', $targetPermission))
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->get(route('admin.permissions.create'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($limitedAdmin)
            ->post(route('admin.permissions.store'), [
                'name' => 'new_permission',
                'display_name' => 'New Permission',
                'description' => 'Should be blocked.',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($limitedAdmin)
            ->get(route('admin.permissions.edit', $targetPermission))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($limitedAdmin)
            ->put(route('admin.permissions.update', $targetPermission), [
                'name' => 'catalog_sync_updated',
                'display_name' => 'Updated',
                'description' => null,
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($limitedAdmin)
            ->delete(route('admin.permissions.destroy', $targetPermission))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('permissions', [
            'id' => $targetPermission->id,
            'name' => 'catalog_sync',
        ]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'new_permission',
        ]);
    }
}
