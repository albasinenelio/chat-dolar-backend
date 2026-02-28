<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tenant A — Kodan (super_admin) ────────────────────────────────────
        $tenantKodan = Tenant::create([
            'id'   => Str::uuid(),
            'name' => 'Loja Kodan',
            'slug' => 'kodan',
        ]);

        // ── Tenant B — Amigo (admin) ──────────────────────────────────────────
        $tenantAmigo = Tenant::create([
            'id'   => Str::uuid(),
            'name' => 'Loja Amigo',
            'slug' => 'amigo',
        ]);

        // ── Kodan — super_admin (vê tudo) ─────────────────────────────────────
        User::create([
            'name'      => 'Kodan',
            'email'     => 'kodanappbuilder@gmail.com',
           'password' => Hash::make('K0d@n#9vXqL2!mRt%$*}&[*#|$'),
            'tenant_id' => $tenantKodan->id,
            'role'      => 'super_admin',
        ]);

        // ── Amigo — admin (vê só o seu tenant) ───────────────────────────────
        User::create([
            'name'      => 'Amigo',
            'email'     => 'Playsenterprise@gmail.com',
          'password' => Hash::make('Am!g0#7wPzN4@kYs%@*}&}$|-$'),
            'tenant_id' => $tenantAmigo->id,
            'role'      => 'admin',
        ]);


        $this->call(ProductSeeder::class);
    }
}