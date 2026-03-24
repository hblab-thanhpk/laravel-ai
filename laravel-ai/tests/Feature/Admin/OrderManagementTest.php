<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_admin_can_view_orders_index(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        Order::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertViewIs('admin.orders.index')
            ->assertViewHas('orders');
    }

    public function test_authenticated_admin_can_view_order_detail(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->pending()->create();

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertViewIs('admin.orders.show')
            ->assertViewHas('order');
    }

    public function test_guest_cannot_access_order_management(): void
    {
        $order = Order::factory()->create();

        $this->get(route('admin.orders.index'))->assertRedirect();
        $this->get(route('admin.orders.show', $order))->assertRedirect();
        $this->get(route('admin.orders.edit', $order))->assertRedirect();
    }

    public function test_non_admin_cannot_access_order_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.orders.index'))
            ->assertRedirect('/');

        $this->actingAs($user)
            ->get(route('admin.orders.show', $order))
            ->assertRedirect('/');
    }

    public function test_admin_can_update_order_status_following_valid_transitions(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->pending()->create();

        // pending → paid
        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'paid'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);

        // paid → shipped
        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'shipped'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);

        // shipped → completed
        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'completed'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    public function test_admin_cannot_perform_invalid_status_transition(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        // pending → shipped (invalid — must go through paid)
        $order = Order::factory()->pending()->create();

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'shipped'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
    }

    public function test_admin_cannot_update_completed_order(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->completed()->create();

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'pending'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    public function test_cancelling_order_restores_product_stock(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->paid()->create();

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => $product->price,
            'subtotal' => (float) $product->price * 3,
            'product_name' => $product->name,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), ['status' => 'cancelled'])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 13]);
    }

    public function test_admin_can_delete_order(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $order = Order::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_orders_index_filters_by_status(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        Order::factory()->pending()->create();
        Order::factory()->paid()->create();
        Order::factory()->completed()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.orders.index', ['status' => 'pending']));

        $response->assertOk();
        $orders = $response->viewData('orders');
        $this->assertSame(1, $orders->total());
    }

    public function test_order_requires_granular_permission_for_update_and_delete(): void
    {
        /** @var User $staffWithViewOnly */
        $staffWithViewOnly = User::factory()->create();
        $staffRole = Role::query()->where('name', 'staff')->first();
        $viewPermission = Permission::query()->where('name', 'orders_view')->first();
        $staffRole->permissions()->sync([$viewPermission->id]);
        $staffWithViewOnly->update(['role_id' => $staffRole->id]);
        $staffWithViewOnly->flushPermissionsCache();

        $order = Order::factory()->pending()->create();

        // has view — can access index and show
        $this->actingAs($staffWithViewOnly)
            ->get(route('admin.orders.index'))
            ->assertOk();

        // no update permission — blocked
        $this->actingAs($staffWithViewOnly)
            ->get(route('admin.orders.edit', $order))
            ->assertRedirect(route('admin.dashboard'));

        // no delete permission — blocked
        $this->actingAs($staffWithViewOnly)
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.dashboard'));
    }
}
