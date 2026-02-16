<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->json('social_links')->nullable()->after('meta_description');
            $table->string('ga_measurement_id', 50)->nullable()->after('social_links');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn(['social_links', 'ga_measurement_id']);
        });
    }
};
