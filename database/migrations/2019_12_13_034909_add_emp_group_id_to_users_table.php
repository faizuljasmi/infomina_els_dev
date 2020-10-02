<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpGroupIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('emp_group_id');
            $table->unsignedBigInteger('emp_group_two_id')->nullable();
            $table->unsignedBigInteger('emp_group_three_id')->nullable();
            $table->unsignedBigInteger('emp_group_four_id')->nullable();
            $table->unsignedBigInteger('emp_group_five_id')->nullable();

            $table->foreign('emp_group_id')->references('id')->on('emp_groups');
            $table->foreign('emp_group_two_id')->references('id')->on('emp_groups');
            $table->foreign('emp_group_three_id')->references('id')->on('emp_groups');
            $table->foreign('emp_group_four_id')->references('id')->on('emp_groups');
            $table->foreign('emp_group_five_id')->references('id')->on('emp_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_emp_group_id_foreign');
            $table->dropColumn('emp_group_id');
            $table->dropForeign('users_emp_group_two_id_foreign');
            $table->dropColumn('emp_group_two_id');
            $table->dropForeign('users_emp_group_three_id_foreign');
            $table->dropColumn('emp_group_three_id');
            $table->dropForeign('users_emp_group_four_id_foreign');
            $table->dropColumn('emp_group_four_id');
            $table->dropForeign('users_emp_group_five_id_foreign');
            $table->dropColumn('emp_group_five_id');
        });
    }
}
