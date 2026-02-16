<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->table('users', function (Blueprint $table) {
            $table->enum('role', ['master', 'admin'])->default('admin')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('two_factor_secret', 255)->nullable()->after('is_active');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            $table->json('allowed_ips')->nullable()->after('two_factor_enabled');
            $table->boolean('ip_restriction_enabled')->default(false)->after('allowed_ips');
            $table->json('permissions')->nullable()->after('ip_restriction_enabled');
            $table->timestamp('last_login_at')->nullable()->after('permissions');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'is_active', 'two_factor_secret', 'two_factor_enabled',
                'allowed_ips', 'ip_restriction_enabled', 'permissions',
                'last_login_at', 'last_login_ip',
            ]);
        });
    }
};
