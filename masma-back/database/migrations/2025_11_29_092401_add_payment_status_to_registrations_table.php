<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('payment_verified')->default(false);
            $table->string('generated_password')->nullable();
            $table->boolean('credentials_sent')->default(false);
            $table->timestamp('credentials_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_verified', 'generated_password', 'credentials_sent', 'credentials_sent_at']);
        });
    }
};