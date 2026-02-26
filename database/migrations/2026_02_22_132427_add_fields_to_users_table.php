<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tenant ao qual o admin pertence
            $table->uuid('tenant_id')->nullable()->after('id');

            // Papel do utilizador no sistema
            $table->enum('role', ['super_admin', 'admin'])
                  ->default('admin')
                  ->after('tenant_id');

            // OTP — código e validade
            $table->string('otp_code', 6)->nullable()->after('remember_token');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');

            // Foreign key para tenants
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'role', 'otp_code', 'otp_expires_at']);
        });
    }
};