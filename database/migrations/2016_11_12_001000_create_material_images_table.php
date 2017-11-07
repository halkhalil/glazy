<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_images', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('material_id')->unsigned()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->integer('orton_cone_id')->unsigned()->nullable()->index();
            $table->integer('atmosphere_id')->unsigned()->nullable()->index();

            $table->tinyInteger('dominant_rgb_r')->unsigned()->nullable();
            $table->tinyInteger('dominant_rgb_g')->unsigned()->nullable();
            $table->tinyInteger('dominant_rgb_b')->unsigned()->nullable();
            $table->tinyInteger('secondary_rgb_r')->unsigned()->nullable();
            $table->tinyInteger('secondary_rgb_g')->unsigned()->nullable();
            $table->tinyInteger('secondary_rgb_b')->unsigned()->nullable();

            $table->string('filename');

            $table->boolean('is_private')->default(false);

            $table->integer('created_by_user_id')->unsigned()->index();
            $table->integer('updated_by_user_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials');
            $table->foreign('created_by_user_id')->references('id')->on('users');
            $table->foreign('updated_by_user_id')->references('id')->on('users');
            $table->foreign('orton_cone_id')->references('id')->on('orton_cones');
            $table->foreign('atmosphere_id')->references('id')->on('atmospheres');
        });

        DB::statement('ALTER TABLE `material_images` ADD FULLTEXT INDEX `material_images_fulltext` (`title`, `description`)');

        Schema::table('materials', function (Blueprint $table) {

            $table->foreign('thumbnail_id')->references('id')->on('material_images');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['thumbnail_id']);
        });

        Schema::drop('material_images');
    }
}
