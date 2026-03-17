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
        Schema::create('get_in_touch', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Get in Touch');
            $table->string('main_title')->default("Let's Work Together!");
            $table->text('description')->nullable();
            $table->string('background_image')->nullable();
            $table->string('button_text')->default('Became A Member');
            $table->string('button_link')->default('/bemember');
            $table->string('button_icon')->default('FaUserPlus');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_in_touch');
    }
};
