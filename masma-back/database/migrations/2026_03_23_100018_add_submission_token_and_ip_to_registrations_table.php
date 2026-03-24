<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmissionTokenAndIpToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('submission_token', 100)->nullable()->after('id');
            $table->string('ip_address', 45)->nullable()->after('submission_token');
            
            // Add index for faster duplicate checking
            $table->index(['office_email', 'mobile', 'created_at']);
            $table->index('submission_token');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['office_email', 'mobile', 'created_at']);
            $table->dropColumn(['submission_token', 'ip_address']);
        });
    }
}