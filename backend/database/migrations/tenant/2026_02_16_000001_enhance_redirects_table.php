<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->table('redirects', function (Blueprint $table) {
            $table->smallInteger('status_code')->default(302)->after('target_url');
            $table->string('description')->nullable()->after('status_code');
            $table->timestamp('last_clicked_at')->nullable()->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('redirects', function (Blueprint $table) {
            $table->dropColumn(['status_code', 'description', 'last_clicked_at']);
        });
    }
};
