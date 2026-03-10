<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->table('posts', function (Blueprint $table) {
            $table->unsignedInteger('popularity_score')->default(0)->after('hero_settings');
            $table->unsignedInteger('gsc_clicks')->default(0)->after('popularity_score');
            $table->unsignedInteger('gsc_impressions')->default(0)->after('gsc_clicks');
            $table->unsignedInteger('ga_page_views')->default(0)->after('gsc_impressions');
            $table->timestamp('popularity_scored_at')->nullable()->after('ga_page_views');

            $table->index('popularity_score');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('posts', function (Blueprint $table) {
            $table->dropIndex(['popularity_score']);
            $table->dropColumn([
                'popularity_score',
                'gsc_clicks',
                'gsc_impressions',
                'ga_page_views',
                'popularity_scored_at',
            ]);
        });
    }
};
