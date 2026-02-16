<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->text('content_prompt_template')->nullable()->after('noindex_mode');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn('content_prompt_template');
        });
    }
};
