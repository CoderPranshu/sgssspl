<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('asset_type', 30)->default('image');
            $table->string('section_name')->nullable();
            $table->string('image_label')->nullable();
            $table->string('reference', 500);
            $table->string('expected_filename');
            $table->text('alt_text')->nullable();
            $table->string('stored_filename')->nullable();
            $table->text('stored_path')->nullable();
            $table->string('status', 20)->default('missing');
            $table->timestamps();

            $table->unique(['page_id', 'reference'], 'page_assets_page_reference_unique');
            $table->index(['page_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_assets');
    }
};
