<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialAtmospheresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_atmospheres', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('material_id')->unsigned()->index();
            $table->integer('atmosphere_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials');
            $table->foreign('atmosphere_id')->references('id')->on('atmospheres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('material_atmospheres');
    }
}
