<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('site_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('category_id')->nullable();
            $table->longText('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('fetured_image')->nullable();
            $table->enum('status', ['published', 'draft']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
