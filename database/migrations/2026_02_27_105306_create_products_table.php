<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // ID interno — UUID, nunca exposto
            $table->uuid('id')->primary();

            // ID público — exposto na loja e nas URLs do chat
            $table->string('public_id')->unique();

            $table->uuid('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();

            // Nome visível apenas para admins no painel
            $table->string('visual_name');

            // Preço em USD
            $table->decimal('price', 10, 2);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('public_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};