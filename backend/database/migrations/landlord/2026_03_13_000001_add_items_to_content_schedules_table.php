<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('content_schedules', function (Blueprint $table) {
            $table->json('items')->nullable()->after('topics');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('content_schedules', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
