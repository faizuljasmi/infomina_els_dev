<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplacementClaimApplyRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacement_claim_apply_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('claim_id');
            $table->float('claim_total_days', 8,1);
            $table->unsignedBigInteger('leave_id');
            $table->float('leave_total_days', 8,1);
            $table->timestamps();

            $table->foreign('claim_id')->references('id')->on('leave_applications');
            $table->foreign('leave_id')->references('id')->on('leave_applications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replacement_claim_apply_relations');
    }
}
