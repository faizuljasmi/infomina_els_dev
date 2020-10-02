<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveEntitlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_entitlements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->unsignedBigInteger('emp_type_id')->nullable();
            $table->float('no_of_days', 8,1);
            $table->timestamps();
            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('emp_type_id')->references('id')->on('emp_types');

            $table->unique(['leave_type_id', 'emp_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('leave_entitlements');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
