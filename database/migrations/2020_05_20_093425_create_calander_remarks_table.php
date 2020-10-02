<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalanderRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calander_remarks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('remark_date_from');
            $table->date('remark_date_to');
            $table->longText('remark_text')->nullable();
            $table->unsignedBigInteger('remark_by');
            $table->timestamps();

            $table->foreign('remark_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calander_remarks');
    }
}
