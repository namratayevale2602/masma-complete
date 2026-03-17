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
         Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Welcome To Our Association');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('badge_number')->default('20');
            $table->string('badge_label')->default('Years');
            $table->string('badge_subtext')->default('of Legacy');
            $table->string('button_text')->default('Read More');
            $table->string('button_link')->default('/about-us');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
