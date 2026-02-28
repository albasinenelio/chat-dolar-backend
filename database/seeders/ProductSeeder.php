<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = '775cff0c-a752-4bf1-abdb-886343f9a1dc';

        $products = [
            // ── $35 ──────────────────────────────────────────────
            [
                'public_id'   => 'AGsjGAjskalsksl',
                'tenant_id'   => $tenantId,
                'visual_name' => 'FIFA 26 PC',
                'price'       => 35.00,
            ],
            [
                'public_id'   => 'GSsksksueieoesj',
                'tenant_id'   => $tenantId,
                'visual_name' => 'EA FC 25 PC',
                'price'       => 35.00,
            ],
            [
                'public_id'   => 'KjHTSGSksskagg',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Call of Duty MW III',
                'price'       => 35.00,
            ],
            [
                'public_id'   => 'MnBvCxZwQrStYpLk',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Hogwarts Legacy PC',
                'price'       => 35.00,
            ],

            // ── $20 ──────────────────────────────────────────────
            [
                'public_id'   => 'TzPmXwRqLsNvBcJy',
                'tenant_id'   => $tenantId,
                'visual_name' => 'GTA V Premium',
                'price'       => 20.00,
            ],
            [
                'public_id'   => 'HdKfYnCgWbSxQrMp',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Red Dead Redemption 2',
                'price'       => 20.00,
            ],
            [
                'public_id'   => 'VjZtNsLmRkXqPwGh',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Cyberpunk 2077',
                'price'       => 20.00,
            ],
            [
                'public_id'   => 'WrPkTnBsXmCqLzFj',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Elden Ring PC',
                'price'       => 20.00,
            ],

            // ── $10 ──────────────────────────────────────────────
            [
                'public_id'   => 'BcFrYpDsKwNqMxTv',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Minecraft Java',
                'price'       => 10.00,
            ],
            [
                'public_id'   => 'QmJsXtLzWcRgNkPb',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Roblox Premium Account',
                'price'       => 10.00,
            ],
            [
                'public_id'   => 'DhSnVfYqGwBmKtCx',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Among Us + DLCs',
                'price'       => 10.00,
            ],
            [
                'public_id'   => 'JkRmPwBtXsNcLqYz',
                'tenant_id'   => $tenantId,
                'visual_name' => 'Fall Guys Premium',
                'price'       => 10.00,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['public_id' => $product['public_id']],
                $product
            );
        }
    }
}