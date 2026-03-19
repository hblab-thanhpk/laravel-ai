<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductSeeder extends Seeder
{
    /**
     * Seed catalog products with deterministic data.
     */
    public function run(): void
    {
        $categoryIdsBySlug = Category::query()->pluck('id', 'slug');

        $products = [
            [
                'category_slug' => 'fashion',
                'name' => 'Basic Cotton T-Shirt',
                'slug' => 'basic-cotton-tshirt',
                'sku' => 'PRD-TSHIRT-001',
                'price' => 199000,
                'stock' => 120,
                'description' => 'Everyday cotton t-shirt with regular fit.',
                'is_active' => true,
            ],
            [
                'category_slug' => 'fashion',
                'name' => 'Classic Polo Shirt',
                'slug' => 'classic-polo-shirt',
                'sku' => 'PRD-POLO-001',
                'price' => 329000,
                'stock' => 80,
                'description' => 'Polo shirt with breathable fabric.',
                'is_active' => true,
            ],
            [
                'category_slug' => 'footwear',
                'name' => 'Urban Running Shoes',
                'slug' => 'urban-running-shoes',
                'sku' => 'PRD-SHOES-001',
                'price' => 899000,
                'stock' => 45,
                'description' => 'Lightweight running shoes for daily training.',
                'is_active' => true,
            ],
            [
                'category_slug' => 'footwear',
                'name' => 'Canvas Slip-On',
                'slug' => 'canvas-slip-on',
                'sku' => 'PRD-SHOES-002',
                'price' => 459000,
                'stock' => 60,
                'description' => 'Minimal slip-on shoes for casual outfits.',
                'is_active' => true,
            ],
            [
                'category_slug' => 'accessories',
                'name' => 'Leather Belt',
                'slug' => 'leather-belt',
                'sku' => 'PRD-BELT-001',
                'price' => 279000,
                'stock' => 70,
                'description' => 'Full-grain leather belt with matte buckle.',
                'is_active' => true,
            ],
            [
                'category_slug' => 'accessories',
                'name' => 'Daily Backpack',
                'slug' => 'daily-backpack',
                'sku' => 'PRD-BAG-001',
                'price' => 649000,
                'stock' => 35,
                'description' => 'Compact backpack with laptop compartment.',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            $categoryId = $categoryIdsBySlug->get($product['category_slug']);

            if ($categoryId === null) {
                throw new RuntimeException("Category [{$product['category_slug']}] does not exist. Seed CategorySeeder first.");
            }

            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $categoryId,
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'description' => $product['description'],
                    'is_active' => $product['is_active'],
                ],
            );
        }
    }
}
