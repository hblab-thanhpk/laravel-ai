<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed catalog categories with deterministic data.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Fashion and apparel products.',
                'is_active' => true,
            ],
            [
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Shoes and sandals for everyday use.',
                'is_active' => true,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Bags, belts, and lifestyle accessories.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
