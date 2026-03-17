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
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('category_title'); // e.g., Public Relations Committee, Women Entrepreneur's Committee
            $table->string('category_icon')->nullable(); // icon name like FaUserTie, FaUserShield
            $table->string('member_name');
            $table->string('member_city');
            $table->string('member_position');
            $table->string('member_image')->nullable();
            $table->integer('category_order')->default(0);
            $table->integer('member_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committees');
    }
};
