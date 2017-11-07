<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_materials', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_material_id')->unsigned()->index();
            $table->integer('component_material_id')->unsigned()->index();
            $table->decimal('percentage_amount',9,4)->default(0);
            $table->boolean('is_additional')->default(false);
            $table->timestamps();

            $table->foreign('parent_material_id')->references('id')->on('materials');
            $table->foreign('component_material_id')->references('id')->on('materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('material_materials');
    }
}
