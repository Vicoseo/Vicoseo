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
            $table->string('fallback_domain', 255)->nullable()->after('sponsor_contact_text');
            $table->text('robots_txt')->nullable()->after('fallback_domain');
            $table->boolean('noindex_mode')->default(false)->after('robots_txt');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn(['fallback_domain', 'robots_txt', 'noindex_mode']);
        });
    }
};
