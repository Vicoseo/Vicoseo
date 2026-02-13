<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->string('login_url', 500)->nullable()->after('entry_url');
        });

        Schema::connection('landlord')->table('global_top_offers', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn('login_url');
        });

        Schema::connection('landlord')->table('global_top_offers', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
