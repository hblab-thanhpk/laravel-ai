<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────

    private function actingAsUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        return $user;
    }

    private function token(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    private function authHeader(User $user): array
    {
        return ['Authorization' => 'Bearer '.$this->token($user)];
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_list_products(): void
    {
        $this->getJson('/api/v1/products')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_products(): void
    {
        $user = $this->actingAsUser();
        Product::factory()->count(5)->create();

        $this->getJson('/api/v1/products', $this->authHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                'status', 'message',
                'data' => [
                    'items' => [['id', 'name', 'slug', 'sku', 'price', 'stock', 'is_active']],
                    'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                ],
            ])
            ->assertJsonPath('data.meta.total', 5);
    }

    public function test_index_can_filter_by_active_status(): void
    {
        $user = $this->actingAsUser();
        Product::factory()->count(3)->create(['is_active' => true]);
        Product::factory()->count(2)->create(['is_active' => false]);

        $this->getJson('/api/v1/products?status=active', $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.meta.total', 3);

        $this->getJson('/api/v1/products?status=inactive', $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.meta.total', 2);
    }

    public function test_index_can_search_by_name(): void
    {
        $user = $this->actingAsUser();
        Product::factory()->create(['name' => 'Laravel T-Shirt', 'slug' => 'laravel-t-shirt']);
        Product::factory()->count(3)->create();

        $this->getJson('/api/v1/products?search=laravel', $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.meta.total', 1);
    }

    // ── SHOW ──────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_product_detail(): void
    {
        $user = $this->actingAsUser();
        $product = Product::factory()->create();

        $this->getJson("/api/v1/products/{$product->id}", $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', $product->name)
            ->assertJsonStructure(['data' => ['id', 'name', 'slug', 'sku', 'price', 'stock', 'category', 'variants']]);
    }

    public function test_show_returns_404_for_nonexistent_product(): void
    {
        $user = $this->actingAsUser();

        $this->getJson('/api/v1/products/nonexistent-id', $this->authHeader($user))
            ->assertNotFound();
    }

    // ── STORE ─────────────────────────────────────────────────────────────────

    public function test_unauthenticated_cannot_create_product(): void
    {
        $this->postJson('/api/v1/products', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = $this->actingAsUser();
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'New Product',
            'slug' => 'new-product',
            'sku' => 'SKU-NEW-001',
            'price' => 99.99,
            'stock' => 10,
            'description' => 'Test description.',
            'is_active' => true,
        ];

        $this->postJson('/api/v1/products', $payload, $this->authHeader($user))
            ->assertCreated()
            ->assertJsonPath('data.name', 'New Product')
            ->assertJsonPath('data.slug', 'new-product')
            ->assertJsonPath('data.sku', 'SKU-NEW-001');

        $this->assertDatabaseHas('products', ['slug' => 'new-product']);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->actingAsUser();

        $this->postJson('/api/v1/products', [], $this->authHeader($user))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug', 'sku', 'price', 'stock']);
    }

    public function test_store_validates_unique_slug_and_sku(): void
    {
        $user = $this->actingAsUser();
        $product = Product::factory()->create(['slug' => 'existing-slug', 'sku' => 'EXISTING-SKU']);

        $this->postJson('/api/v1/products', [
            'name' => 'Another Product',
            'slug' => 'existing-slug',  // duplicate
            'sku' => 'EXISTING-SKU',   // duplicate
            'price' => 10,
            'stock' => 5,
            'is_active' => true,
        ], $this->authHeader($user))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug', 'sku']);
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_update_product(): void
    {
        $user = $this->actingAsUser();
        $product = Product::factory()->create(['name' => 'Old Name']);

        $this->putJson("/api/v1/products/{$product->id}", [
            'name' => 'Updated Name',
            'slug' => $product->slug,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'is_active' => true,
        ], $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_update_ignores_own_slug_and_sku_uniqueness(): void
    {
        $user = $this->actingAsUser();
        $product = Product::factory()->create();

        // Gửi lại chính slug/sku của product → không được báo lỗi unique
        $this->putJson("/api/v1/products/{$product->id}", [
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'is_active' => $product->is_active,
        ], $this->authHeader($user))
            ->assertOk();
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_delete_product(): void
    {
        $user = $this->actingAsUser();
        $product = Product::factory()->create();

        $this->deleteJson("/api/v1/products/{$product->id}", [], $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_unauthenticated_cannot_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/v1/products/{$product->id}")->assertUnauthorized();
    }
}
