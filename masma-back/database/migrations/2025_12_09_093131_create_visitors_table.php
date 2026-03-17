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
        Schema::create('visitors', function (Blueprint $table) {
           $table->id();
            $table->string('visitor_name');
            $table->string('bussiness_name')->nullable();
            $table->string('mobile');
            $table->string('phone')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('email');
            $table->string('city')->nullable();
            $table->string('town')->nullable();
            $table->string('village')->nullable();
            $table->text('remark')->nullable();
            $table->string('qr_code_path')->nullable(); // Store QR code file path
            $table->string('qr_code_data')->nullable(); // Store encoded data for QR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
