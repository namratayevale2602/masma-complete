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
       Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->integer('row')->default(1); // 1 for first row, 2 for second row
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('alt_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
