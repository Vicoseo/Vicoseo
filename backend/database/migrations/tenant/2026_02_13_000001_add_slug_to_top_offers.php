<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('top_offers', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('top_offers', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
