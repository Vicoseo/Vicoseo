<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->string('earnings_image', 500)->nullable()->after('custom_css');
            $table->string('earnings_video_url', 500)->nullable()->after('earnings_image');
            $table->string('earnings_title', 255)->nullable()->after('earnings_video_url');
            $table->text('earnings_content')->nullable()->after('earnings_title');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn(['earnings_image', 'earnings_video_url', 'earnings_title', 'earnings_content']);
        });
    }
};
