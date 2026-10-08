<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->longText('custom_js')->nullable()->after('custom_css');
            $table->boolean('swiper_enabled')->default(false)->after('custom_js');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['custom_js', 'swiper_enabled']);
        });
    }
};
