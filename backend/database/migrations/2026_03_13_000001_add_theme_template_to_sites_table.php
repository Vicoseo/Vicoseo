<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->string('theme_template', 50)->default('default')->after('custom_css');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn('theme_template');
        });
    }
};
