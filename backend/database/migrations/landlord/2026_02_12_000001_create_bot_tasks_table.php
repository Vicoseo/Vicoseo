<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('bot_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // clone, ai_generate, ai_spin, sitemap, gsc_submit
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->foreignId('source_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('target_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('batch_id')->nullable();
            $table->json('payload')->nullable();
            $table->json('result')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('bot_tasks');
    }
};
