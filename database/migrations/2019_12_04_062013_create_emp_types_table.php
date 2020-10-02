<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            // $table->integer('ent_annual')->nullable();
            // $table->integer('ent_calamity')->nullable();
            // $table->integer('ent_carryfwd')->nullable();
            // $table->integer('ent_compassionate')->nullable();
            // $table->integer('ent_emergency')->nullable();
            // $table->integer('ent_hospitalization')->nullable();
            // $table->integer('ent_marriage')->nullable();
            // $table->integer('ent_maternity')->nullable();
            // $table->integer('ent_paternity')->nullable();
            // $table->integer('ent_sick')->nullable();
            // $table->integer('ent_training')->nullable();
            // $table->integer('ent_unpaid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_types');
    }
}
