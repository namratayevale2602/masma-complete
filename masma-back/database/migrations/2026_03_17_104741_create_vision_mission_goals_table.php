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
        Schema::create('vision_mission_goals', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['vision', 'mission', 'goal']);
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->json('items')->nullable(); // For highlights, points, categories
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
        Schema::dropIfExists('vision_mission_goals');
    }
};
