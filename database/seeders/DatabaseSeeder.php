<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tenant A — Kodan (super_admin) ────────────────────────────────────
        $tenantKodan = Tenant::create([
            'id'             => 'b094d1e2-37f7-4039-abe0-6f525a3722c2',
            'name'           => 'Loja Kodan',
            'slug'           => 'kodan',
            'btc_address'    => '',
            'paypal_address' => '',
        ]);

        // ── Tenant B — Phoenix (admin) ────────────────────────────────────────
        $tenantPhoenix = Tenant::create([
            'id'             => '775cff0c-a752-4bf1-abdb-886343f9a1dc',
            'name'           => 'Loja Phoenix',
            'slug'           => 'phoenix',
            'btc_address'    => '1MPa1fSFYYWADLC7xvZpXh5VM1mDp2mjGD',
            'paypal_address' => 'seller@paypal.com',
        ]);

        // ── Kodan — super_admin ───────────────────────────────────────────────
        User::create([
            'name'      => 'Kodan',
            'email'     => 'kodanappbuilder@gmail.com',
            'password'  => Hash::make('K0d@n#9vXqL2!mRt%$*}&[*#|$'),
            'tenant_id' => $tenantKodan->id,
            'role'      => 'super_admin',
        ]);

        // ── Phoenix — admin ───────────────────────────────────────────────────
        User::create([
            'name'      => 'Phoenix',
            'email'     => 'Playsenterprise@gmail.com',
            'password'  => Hash::make('Am!g0#7wPzN4@kYs%@*}&}$|-$'),
            'tenant_id' => $tenantPhoenix->id,
            'role'      => 'admin',
        ]);

        $this->call(ProductSeeder::class);
    }
}