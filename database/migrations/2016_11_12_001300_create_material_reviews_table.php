<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_reviews', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('material_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('rating')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials');
            $table->foreign('user_id')->references('id')->on('users');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('material_reviews');
    }
}
