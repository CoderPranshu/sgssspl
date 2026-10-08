<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('content_mode', 20)->default('normal')->after('body');
            $table->longText('custom_html')->nullable()->after('content_mode');
            $table->longText('custom_css')->nullable()->after('custom_html');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['content_mode', 'custom_html', 'custom_css']);
        });
    }
};
