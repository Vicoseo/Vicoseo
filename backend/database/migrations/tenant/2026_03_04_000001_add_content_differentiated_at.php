<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->timestamp('content_differentiated_at')->nullable();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('content_differentiated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('content_differentiated_at');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('content_differentiated_at');
        });
    }
};
