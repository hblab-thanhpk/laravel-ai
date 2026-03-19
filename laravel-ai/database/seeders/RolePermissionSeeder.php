<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed core roles and permissions for admin panel.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'users_view',
                'display_name' => 'Xem danh sách users',
                'description' => 'Xem danh sách và chi tiết người dùng.',
            ],
            [
                'name' => 'users_create',
                'display_name' => 'Tạo users',
                'description' => 'Tạo người dùng mới.',
            ],
            [
                'name' => 'users_update',
                'display_name' => 'Cập nhật users',
                'description' => 'Chỉnh sửa thông tin người dùng.',
            ],
            [
                'name' => 'users_delete',
                'display_name' => 'Xóa users',
                'description' => 'Xóa người dùng.',
            ],
            [
                'name' => 'roles_view',
                'display_name' => 'Xem danh sách roles',
                'description' => 'Xem danh sách và chi tiết role.',
            ],
            [
                'name' => 'roles_create',
                'display_name' => 'Tạo roles',
                'description' => 'Tạo role mới.',
            ],
            [
                'name' => 'roles_update',
                'display_name' => 'Cập nhật roles',
                'description' => 'Chỉnh sửa role.',
            ],
            [
                'name' => 'roles_delete',
                'display_name' => 'Xóa roles',
                'description' => 'Xóa role.',
            ],
            [
                'name' => 'permissions_view',
                'display_name' => 'Xem danh sách permissions',
                'description' => 'Xem danh sách và chi tiết permission.',
            ],
            [
                'name' => 'permissions_create',
                'display_name' => 'Tạo permissions',
                'description' => 'Tạo permission mới.',
            ],
            [
                'name' => 'permissions_update',
                'display_name' => 'Cập nhật permissions',
                'description' => 'Chỉnh sửa permission.',
            ],
            [
                'name' => 'permissions_delete',
                'display_name' => 'Xóa permissions',
                'description' => 'Xóa permission.',
            ],
            [
                'name' => 'categories_view',
                'display_name' => 'Xem danh sách categories',
                'description' => 'Xem danh sách và chi tiết danh mục.',
            ],
            [
                'name' => 'categories_create',
                'display_name' => 'Tạo categories',
                'description' => 'Tạo danh mục mới.',
            ],
            [
                'name' => 'categories_update',
                'display_name' => 'Cập nhật categories',
                'description' => 'Chỉnh sửa danh mục.',
            ],
            [
                'name' => 'categories_delete',
                'display_name' => 'Xóa categories',
                'description' => 'Xóa danh mục.',
            ],
            [
                'name' => 'products_view',
                'display_name' => 'Xem danh sách products',
                'description' => 'Xem danh sách và chi tiết sản phẩm.',
            ],
            [
                'name' => 'products_create',
                'display_name' => 'Tạo products',
                'description' => 'Tạo sản phẩm mới.',
            ],
            [
                'name' => 'products_update',
                'display_name' => 'Cập nhật products',
                'description' => 'Chỉnh sửa sản phẩm.',
            ],
            [
                'name' => 'products_delete',
                'display_name' => 'Xóa products',
                'description' => 'Xóa sản phẩm.',
            ],
            [
                'name' => 'product_variants_view',
                'display_name' => 'Xem danh sách product variants',
                'description' => 'Xem danh sách biến thể theo sản phẩm.',
            ],
            [
                'name' => 'product_variants_create',
                'display_name' => 'Tạo product variants',
                'description' => 'Tạo biến thể sản phẩm.',
            ],
            [
                'name' => 'product_variants_update',
                'display_name' => 'Cập nhật product variants',
                'description' => 'Chỉnh sửa biến thể sản phẩm.',
            ],
            [
                'name' => 'product_variants_delete',
                'display_name' => 'Xóa product variants',
                'description' => 'Xóa biến thể sản phẩm.',
            ],
            [
                'name' => 'manage_products',
                'display_name' => 'Quản lý sản phẩm',
                'description' => 'Quản trị sản phẩm trong hệ thống.',
            ],
            [
                'name' => 'manage_inventory',
                'display_name' => 'Quản lý tồn kho',
                'description' => 'Điều chỉnh và theo dõi tồn kho.',
            ],
            [
                'name' => 'manage_orders',
                'display_name' => 'Quản lý đơn hàng',
                'description' => 'Xử lý và theo dõi đơn hàng.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                    'is_system' => true,
                ],
            );
        }

        $permissionMap = Permission::query()
            ->pluck('id', 'name');

        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Toàn quyền quản trị hệ thống.',
                'permission_names' => array_keys($permissionMap->all()),
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Nhân viên quản lý sản phẩm/đơn hàng/tồn kho.',
                'permission_names' => [
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
                    'manage_products',
                    'manage_inventory',
                    'manage_orders',
                ],
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Người dùng cuối, không có quyền admin.',
                'permission_names' => [],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::query()->updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_system' => true,
                ],
            );

            $role->permissions()->sync(
                collect($roleData['permission_names'])
                    ->map(static fn (string $permissionName): ?string => $permissionMap->get($permissionName))
                    ->filter()
                    ->values()
                    ->all(),
            );
        }

        $adminRoleId = Role::query()->where('name', 'admin')->value('id');
        $customerRoleId = Role::query()->where('name', 'customer')->value('id');

        if ($adminRoleId !== null) {
            User::query()
                ->where('is_admin', true)
                ->update([
                    'role_id' => $adminRoleId,
                ]);
        }

        if ($customerRoleId !== null) {
            User::query()
                ->whereNull('role_id')
                ->update([
                    'role_id' => $customerRoleId,
                    'is_admin' => false,
                ]);
        }
    }
}
