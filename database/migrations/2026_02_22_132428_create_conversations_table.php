<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Tenant (loja) a que pertence esta conversa
            $table->uuid('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();

            // Dados do visitante (sem conta)
            $table->string('visitor_id');              // ex: user-0001
            $table->string('visitor_name');            // nome fornecido no modal

            // Produto em contexto (opcional — vindo de ?productId=)
            $table->string('product_id')->nullable();

            // Meta
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);

            $table->timestamps();

            // Índices
            $table->index('tenant_id');
            $table->index('visitor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};