<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('conversation_id');
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->cascadeOnDelete();

            // Quem enviou
            $table->enum('sender_type', ['visitor', 'admin']);

            // Tipo de conteúdo
            $table->enum('type', ['text', 'image'])->default('text');

            // Conteúdo principal (texto ou alt text para imagens)
            $table->text('content');

            // Campos opcionais para imagens
            $table->string('image_url')->nullable();
            $table->string('caption')->nullable();

            // Estado de leitura
            $table->boolean('read')->default(false);

            $table->timestamps();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};