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

            $table->uuid('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();

            $table->string('visitor_id');
            $table->string('visitor_name');
            $table->string('product_id')->nullable();

            $table->timestamp('last_message_at')->nullable();
            $table->text('last_message')->nullable();        // ← novo
            $table->unsignedInteger('unread_count')->default(0);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('visitor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};