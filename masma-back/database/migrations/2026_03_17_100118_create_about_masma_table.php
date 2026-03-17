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
        Schema::create('about_masma', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Welcome To Our Association');
            $table->string('president_name')->nullable();
            $table->string('president_title')->nullable();
            $table->string('president_image')->nullable();
            $table->text('president_message')->nullable();
            $table->text('president_message_2')->nullable();
            $table->text('president_message_3')->nullable();
            $table->string('stats_1_label')->nullable();
            $table->string('stats_1_value')->nullable();
            $table->string('stats_2_label')->nullable();
            $table->string('stats_2_value')->nullable();
            $table->string('stats_3_label')->nullable();
            $table->string('stats_3_value')->nullable();
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
        Schema::dropIfExists('about_masma');
    }
};
