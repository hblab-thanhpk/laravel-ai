<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_price' => fake()->randomFloat(2, 50, 10000),
            'status' => OrderStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }

    public function paid(): static
    {
        return $this->state(['status' => OrderStatus::Paid]);
    }

    public function shipped(): static
    {
        return $this->state(['status' => OrderStatus::Shipped]);
    }

    public function completed(): static
    {
        return $this->state(['status' => OrderStatus::Completed]);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => OrderStatus::Cancelled]);
    }
}
