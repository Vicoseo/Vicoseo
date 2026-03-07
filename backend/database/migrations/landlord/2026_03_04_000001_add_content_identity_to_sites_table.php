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
            $table->json('content_identity')->nullable()->after('content_prompt_template');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn('content_identity');
        });
    }
};
