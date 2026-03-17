<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldssToRegistrations extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Add missing fields
            $table->string('payment_mode')->nullable()->after('registration_amount');
            $table->string('transaction_reference')->nullable()->after('payment_mode');
            $table->string('visiting_card_path')->nullable()->after('applicant_photo_path');
            $table->string('payment_screenshot_path')->nullable()->after('visiting_card_path');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_verified');
            $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_verified_at');
            $table->text('payment_remarks')->nullable()->after('payment_verified_by');
            
            // Add index for better performance
            $table->index('payment_verified');
            $table->index('payment_mode');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_mode',
                'transaction_reference',
                'visiting_card_path',
                'payment_screenshot_path',
                'payment_verified_at',
                'payment_verified_by',
                'payment_remarks'
            ]);
        });
    }
}