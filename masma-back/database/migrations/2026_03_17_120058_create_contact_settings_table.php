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
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_title')->default('Contact Us');
            $table->text('page_description')->nullable();
            $table->json('contact_info')->nullable(); // Store all contact info cards as JSON
            $table->string('office_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('working_hours_weekdays')->nullable();
            $table->string('working_hours_saturday')->nullable();
            $table->string('map_embed_url')->nullable();
            $table->string('form_title')->default('Send Us a Message');
            $table->text('form_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
