<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->text('endpoint');

            // Hash SHA256 do endpoint — usado para garantir unicidade (evita TEXT em UNIQUE)
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
    }
};