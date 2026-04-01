<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeMemberIdNullableInRegistrations extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Make member_id nullable
            $table->string('member_id')->nullable()->change();
            
            // Ensure parent_member_id is nullable
            if (Schema::hasColumn('registrations', 'parent_member_id')) {
                $table->string('parent_member_id')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('member_id')->nullable(false)->change();
        });
    }
}