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
            $table->boolean('sponsor_page_visible')->default(true)->after('show_sponsors');
            $table->string('sponsor_contact_url', 500)->nullable()->after('sponsor_page_visible');
            $table->string('sponsor_contact_text', 255)->nullable()->after('sponsor_contact_url');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('sites', function (Blueprint $table) {
            $table->dropColumn(['sponsor_page_visible', 'sponsor_contact_url', 'sponsor_contact_text']);
        });
    }
};
