<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberIdToRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Add member_id column
            if (!Schema::hasColumn('registrations', 'member_id')) {
                $table->string('member_id', 50)->nullable()->unique()->after('id');
            }
            
            // Add index for faster lookups
            if (!Schema::hasIndex('registrations', 'idx_member_id')) {
                $table->index('member_id', 'idx_member_id');
            }
            
            // Add parent_member_id for renewals to link to original member
            if (!Schema::hasColumn('registrations', 'parent_member_id')) {
                $table->string('parent_member_id', 50)->nullable()->after('member_id');
                $table->index('parent_member_id', 'idx_parent_member_id');
            }
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('idx_parent_member_id');
            $table->dropColumn('parent_member_id');
            $table->dropIndex('idx_member_id');
            $table->dropColumn('member_id');
        });
    }
}