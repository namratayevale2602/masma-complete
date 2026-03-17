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
        Schema::create('cta_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Exhibitor, Visitor, Member
            $table->text('description'); // Card description text
            $table->string('icon')->nullable(); // Icon name like 'FaIndustry', 'FaUsers', 'FaUserTie'
            $table->string('color')->nullable(); // Hex color code like '#005aa8', '#ed6605'
            $table->string('stats')->nullable(); // e.g., '500+ Booths', '15,000+ Visitors', '200+ Speakers'
            $table->string('link')->nullable(); // Registration link
            $table->string('button_text')->default('Register'); // Button text
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cta_cards');
    }
};
