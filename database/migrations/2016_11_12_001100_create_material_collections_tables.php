<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialCollectionsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::create('collection_types', function (Blueprint $table) {

			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name');
			$table->text('description')->nullable();
			$table->softDeletes();
		});

		Schema::create('collections', function (Blueprint $table) {

			$table->engine = 'InnoDB';

			$table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->index();
			$table->integer('collection_type_id')->unsigned()->index();
			$table->string('name');
			$table->text('description')->nullable();

			$table->integer('material_count')->unsigned()->default(0);

			$table->string('image_filename')->nullable();

			$table->integer('thumbnail_id')->unsigned()->nullable();

			$table->boolean('is_private')->default(false);
			$table->integer('created_by_user_id')->unsigned()->index();
			$table->integer('updated_by_user_id')->unsigned()->nullable();
			$table->timestamps();

            $table->foreign('parent_id')->references('id')->on('collections');
			$table->foreign('collection_type_id')->references('id')->on('collection_types');
			$table->foreign('thumbnail_id')->references('id')->on('material_images');
			$table->foreign('created_by_user_id')->references('id')->on('users');
			$table->foreign('updated_by_user_id')->references('id')->on('users');

        });

        DB::statement('ALTER TABLE `collections` ADD FULLTEXT INDEX `collections_fulltext` (`name`, `description`)');

        Schema::create('collection_materials', function (Blueprint $table) {

			$table->engine = 'InnoDB';

			$table->increments('id');
            $table->integer('collection_id')->unsigned()->index();
            $table->integer('material_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('material_id')->references('id')->on('materials');
        });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('collection_materials');
        Schema::drop('collections');
        Schema::drop('collection_types');
	}

}
