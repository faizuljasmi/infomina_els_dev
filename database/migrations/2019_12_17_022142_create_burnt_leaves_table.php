<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBurntLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('burnt_leaves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->float('no_of_days', 8,1);
            $table->timestamps();

            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['leave_type_id', 'user_id']);
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
        Schema::dropIfExists('burnt_leaves');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
