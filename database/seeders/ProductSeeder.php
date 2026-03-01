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
            // ── $35 ──────────────────────────────────────────────────────────
            ['public_id' => 'AGsjGAjskalsksl', 'visual_name' => 'FIFA 26 PC',            'price' => 35.00],
            ['public_id' => 'GSsksksueieoesj', 'visual_name' => 'EA FC 25 PC',           'price' => 35.00],
            ['public_id' => 'KjHTSGSksskagg',  'visual_name' => 'Call of Duty MW III',   'price' => 35.00],
            ['public_id' => 'MnBvCxZwQrStYpLk','visual_name' => 'Hogwarts Legacy PC',   'price' => 35.00],

            // ── $20 ──────────────────────────────────────────────────────────
            ['public_id' => 'TzPmXwRqLsNvBcJy','visual_name' => 'GTA V Premium',        'price' => 20.00],
            ['public_id' => 'HdKfYnCgWbSxQrMp','visual_name' => 'Red Dead Redemption 2','price' => 20.00],
            ['public_id' => 'VjZtNsLmRkXqPwGh','visual_name' => 'Cyberpunk 2077',       'price' => 20.00],
            ['public_id' => 'WrPkTnBsXmCqLzFj','visual_name' => 'Elden Ring PC',        'price' => 20.00],

            // ── $10 ──────────────────────────────────────────────────────────
            ['public_id' => 'BcFrYpDsKwNqMxTv','visual_name' => 'Minecraft Java',       'price' => 10.00],
            ['public_id' => 'QmJsXtLzWcRgNkPb','visual_name' => 'Roblox Premium Account','price' => 10.00],
            ['public_id' => 'DhSnVfYqGwBmKtCx','visual_name' => 'Among Us + DLCs',      'price' => 10.00],
            ['public_id' => 'JkRmPwBtXsNcLqYz','visual_name' => 'Fall Guys Premium',    'price' => 10.00],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, ['tenant_id' => $tenantId]));
        }
    }
}