<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_admin_can_manage_categories_products_and_variants(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $storeCategoryResponse = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Thời Trang Nam',
                'slug' => 'thoi-trang-nam',
                'description' => 'Danh mục thời trang nam.',
                'is_active' => '1',
            ]);

        $category = Category::query()->where('slug', 'thoi-trang-nam')->first();

        $this->assertNotNull($category);
        $storeCategoryResponse->assertRedirect(route('admin.categories.show', $category));

        $storeProductResponse = $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Áo Thun Basic',
                'slug' => 'ao-thun-basic',
                'sku' => 'PRD-BASIC-TSHIRT',
                'price' => '199000',
                'stock' => '50',
                'description' => 'Sản phẩm test.',
                'is_active' => '1',
            ]);

        $product = Product::query()->where('sku', 'PRD-BASIC-TSHIRT')->first();

        $this->assertNotNull($product);
        $storeProductResponse->assertRedirect(route('admin.products.show', $product));

        $storeVariantResponse = $this->actingAs($admin)
            ->post(route('admin.products.variants.store', $product), [
                'sku' => 'VAR-BASIC-RED-M',
                'size' => 'M',
                'color' => 'Red',
                'price' => '209000',
                'stock' => '15',
                'is_active' => '1',
            ]);

        $variant = ProductVariant::query()->where('sku', 'VAR-BASIC-RED-M')->first();

        $this->assertNotNull($variant);
        $storeVariantResponse->assertRedirect(route('admin.products.variants.index', $product));

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $category->id,
                'name' => 'Áo Thun Basic 2026',
                'slug' => 'ao-thun-basic-2026',
                'sku' => 'PRD-BASIC-TSHIRT',
                'price' => '229000',
                'stock' => '60',
                'description' => 'Đã cập nhật.',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.products.show', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Áo Thun Basic 2026',
            'slug' => 'ao-thun-basic-2026',
            'price' => '229000.00',
            'stock' => 60,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.variants.update', [$product, $variant]), [
                'sku' => 'VAR-BASIC-RED-M',
                'size' => 'M',
                'color' => 'Red',
                'price' => '239000',
                'stock' => '12',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.products.variants.index', $product));

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'price' => '239000.00',
            'stock' => 12,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.variants.destroy', [$product, $variant]))
            ->assertRedirect(route('admin.products.variants.index', $product));

        $this->assertDatabaseMissing('product_variants', [
            'id' => $variant->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_non_admin_cannot_access_catalog_management(): void
    {
        /** @var User $member */
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('admin.categories.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->get(route('admin.products.index'))
            ->assertRedirect('/')
            ->assertSessionHas('error');
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_catalog_user_requires_granular_permission_for_write_actions(): void
    {
        $role = Role::query()->create([
            'name' => 'catalog_view_only',
            'display_name' => 'Catalog View Only',
            'description' => null,
            'is_system' => false,
        ]);

        $viewPermission = Permission::query()
            ->where('name', 'products_view')
            ->firstOrFail();

        $role->permissions()->sync([$viewPermission->id]);

        /** @var User $limitedAdmin */
        $limitedAdmin = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($limitedAdmin)
            ->get(route('admin.products.index'))
            ->assertOk();

        $this->actingAs($limitedAdmin)
            ->get(route('admin.products.create'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($limitedAdmin)
            ->post(route('admin.products.store'), [
                'name' => 'Blocked Product',
                'slug' => 'blocked-product',
                'sku' => 'PRD-BLOCKED-001',
                'price' => '1000',
                'stock' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('products', [
            'sku' => 'PRD-BLOCKED-001',
        ]);
    }
}
