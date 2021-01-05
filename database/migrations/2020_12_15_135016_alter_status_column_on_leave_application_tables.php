<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusColumnOnLeaveApplicationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE leave_applications MODIFY status ENUM('PENDING_1', 'PENDING_2','PENDING_3','APPROVED', 'DENIED_1','DENIED_2','DENIED_3','CANCELLED','TAKEN','EXPIRED')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE leave_applications status ENUM('PENDING_1', 'PENDING_2','PENDING_3','APPROVED', 'DENIED_1','DENIED_2','DENIED_3','CANCELLED')");
    }
}
