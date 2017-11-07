<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_types', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('concatenated_name');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->tinyInteger('rgb_r')->unsigned()->default(255);
            $table->tinyInteger('rgb_g')->unsigned()->default(255);
            $table->tinyInteger('rgb_b')->unsigned()->default(255);
            $table->integer('count')->unsigned()->default(0);
            $table->integer('old_recipe_type_id')->unsigned()->nullable();
            $table->integer('old_material_type_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('material_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('material_types');
    }
}