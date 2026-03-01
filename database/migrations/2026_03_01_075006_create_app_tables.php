<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Conversations ─────────────────────────────────────────────────────
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
            $table->text('last_message')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('visitor_id');
            $table->index('archived_at');
        });

        // ── Messages ──────────────────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('conversation_id');
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->cascadeOnDelete();

            $table->enum('sender_type', ['visitor', 'admin']);
            $table->enum('type', ['text', 'image'])->default('text');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('read')->default(false);

            $table->timestamps();

            $table->index('conversation_id');
        });

        // ── Products ──────────────────────────────────────────────────────────
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('public_id')->unique();

            $table->uuid('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();

            $table->string('visual_name');
            $table->decimal('price', 10, 2);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('public_id');
        });

        // ── Push Subscriptions ────────────────────────────────────────────────
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->string('public_key', 255);
            $table->string('auth_token', 255);
            $table->string('device_id')->nullable();

            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};