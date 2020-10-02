<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->enum('status', ['PENDING_1', 'PENDING_2','PENDING_3','APPROVED', 'DENIED_1','DENIED_2','DENIED_3','CANCELLED' ])->default('PENDING_1');
            $table->unsignedBigInteger('approver_id_1')->nullable();
            $table->unsignedBigInteger('approver_id_2')->nullable();
            $table->unsignedBigInteger('approver_id_3')->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->string('apply_for');
            $table->date('date_resume');
            $table->float('total_days', 8,1);
            $table->longText('reason');
            $table->unsignedBigInteger('relief_personnel_id')->nullable();
            $table->string('attachment')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_no');
            $table->longText('remarks')->nullable();
            $table->timestamps();

            //Foreign keys
            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('approver_id_1')->references('id')->on('users');
            $table->foreign('approver_id_2')->references('id')->on('users');
            $table->foreign('approver_id_3')->references('id')->on('users');
            $table->foreign('relief_personnel_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_applications');
    }
}
