<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_name');
            $table->date('date_of_birth');
            $table->string('organization')->nullable();
            $table->string('mobile');
            $table->string('phone')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('office_email');
            $table->string('city')->nullable();
            $table->string('town')->nullable();
            $table->string('village')->nullable();
            $table->string('website')->nullable();
            $table->enum('organization_type', [
                'sole_proprietorship',
                'partnership',
                'limited_liability_partnership',
                'private_limited_company',
                'public_limited_company',
                'one_person_company',
                'other'
            ])->nullable();
            $table->enum('business_category', [
                'student',
                'plumber',
                'electrician',
                'installer_solar_pv',
                'solar_water_heater',
                'supplier',
                'dealer',
                'distributor',
                'associate_member',
                'manufacturer'
            ])->nullable();
            $table->date('date_of_incorporation')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('about_service')->nullable();
            $table->string('membership_reference_1');
            $table->string('membership_reference_2');
            $table->enum('registration_type', [
                'renew_epc_classic',
                'student',
                'installer',
                'epc_classic',
                'epc_lifetime',
                'dealer_distributor',
                'silver_corporate',
                'gold_corporate',
                'masma_associates'
            ]);
            $table->decimal('registration_amount', 10, 2);
            $table->boolean('declaration')->default(false);
            $table->string('applicant_photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};