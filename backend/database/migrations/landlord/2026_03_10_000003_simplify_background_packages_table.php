<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('background_packages', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->nullOnDelete();

            $table->dropColumn(['slug', 'description', 'thumbnail_url', 'overlay_blend', 'tags', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('background_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');

            $table->string('slug', 100)->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('thumbnail_url', 500)->nullable()->after('image_url');
            $table->string('overlay_blend', 50)->default('multiply')->after('overlay_color');
            $table->json('tags')->nullable()->after('overlay_blend');
            $table->integer('sort_order')->default(0)->after('tags');
        });
    }
};
