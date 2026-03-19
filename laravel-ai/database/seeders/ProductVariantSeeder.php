<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductVariantSeeder extends Seeder
{
    /**
     * Seed product variants with deterministic data.
     */
    public function run(): void
    {
        $productIdsBySku = Product::query()->pluck('id', 'sku');

        $variants = [
            [
                'product_sku' => 'PRD-TSHIRT-001',
                'sku' => 'VAR-TSHIRT-001-S-BLACK',
                'size' => 'S',
                'color' => 'Black',
                'price' => 199000,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-TSHIRT-001',
                'sku' => 'VAR-TSHIRT-001-M-BLACK',
                'size' => 'M',
                'color' => 'Black',
                'price' => 199000,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-TSHIRT-001',
                'sku' => 'VAR-TSHIRT-001-L-WHITE',
                'size' => 'L',
                'color' => 'White',
                'price' => 209000,
                'stock' => 24,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-POLO-001',
                'sku' => 'VAR-POLO-001-M-NAVY',
                'size' => 'M',
                'color' => 'Navy',
                'price' => 329000,
                'stock' => 26,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-POLO-001',
                'sku' => 'VAR-POLO-001-L-GREEN',
                'size' => 'L',
                'color' => 'Green',
                'price' => 339000,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-SHOES-001',
                'sku' => 'VAR-SHOES-001-40-BLACK',
                'size' => '40',
                'color' => 'Black',
                'price' => 899000,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-SHOES-001',
                'sku' => 'VAR-SHOES-001-41-WHITE',
                'size' => '41',
                'color' => 'White',
                'price' => 899000,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-SHOES-002',
                'sku' => 'VAR-SHOES-002-39-BEIGE',
                'size' => '39',
                'color' => 'Beige',
                'price' => 459000,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-SHOES-002',
                'sku' => 'VAR-SHOES-002-40-BEIGE',
                'size' => '40',
                'color' => 'Beige',
                'price' => 459000,
                'stock' => 18,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-BELT-001',
                'sku' => 'VAR-BELT-001-M-BROWN',
                'size' => 'M',
                'color' => 'Brown',
                'price' => 279000,
                'stock' => 22,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-BELT-001',
                'sku' => 'VAR-BELT-001-L-BLACK',
                'size' => 'L',
                'color' => 'Black',
                'price' => 279000,
                'stock' => 19,
                'is_active' => true,
            ],
            [
                'product_sku' => 'PRD-BAG-001',
                'sku' => 'VAR-BAG-001-STD-BLACK',
                'size' => 'STD',
                'color' => 'Black',
                'price' => 649000,
                'stock' => 14,
                'is_active' => true,
            ],
        ];

        foreach ($variants as $variant) {
            $productId = $productIdsBySku->get($variant['product_sku']);

            if ($productId === null) {
                throw new RuntimeException("Product [{$variant['product_sku']}] does not exist. Seed ProductSeeder first.");
            }

            ProductVariant::query()->updateOrCreate(
                ['sku' => $variant['sku']],
                [
                    'product_id' => $productId,
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'is_active' => $variant['is_active'],
                ],
            );
        }
    }
}
