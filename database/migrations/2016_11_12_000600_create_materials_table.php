<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('old_id')->unsigned()->nullable();

            $table->integer('parent_id')->unsigned()->nullable()->index();

            $table->string('name')->index();
            $table->string('other_names')->nullable();
            $table->string('insight_name')->nullable();

            $table->string('code')->nullable();

            $table->text('description')->nullable();

            $table->integer('material_type_id')->unsigned()->nullable()->index();

            // TODO: needs thinking
            $table->boolean('is_analysis')->default(false);
            $table->boolean('is_primitive')->default(false);
            $table->boolean('is_theoretical')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->integer('from_orton_cone_id')->unsigned()->nullable()->index();
            $table->integer('to_orton_cone_id')->unsigned()->nullable()->index();

            $table->integer('surface_type_id')->unsigned()->nullable()->index();
            $table->integer('transparency_type_id')->unsigned()->nullable()->index();

            $table->integer('country_id')->unsigned()->nullable()->index();

            // COLOR
            $table->string('color_name')->nullable();
            $table->tinyInteger('rgb_r')->unsigned()->nullable();
            $table->tinyInteger('rgb_g')->unsigned()->nullable();
            $table->tinyInteger('rgb_b')->unsigned()->nullable();

            $table->integer('thumbnail_id')->unsigned()->nullable();

            // RATINGS
            $table->integer('rating_total')->unsigned()->default(0);
            $table->integer('rating_number')->unsigned()->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);

            $table->string('base_composite_hash')->nullable()->index();
			$table->string('additive_composite_hash')->nullable()->index();

            $table->boolean('is_private')->default(false);
            $table->integer('created_by_user_id')->unsigned()->index();
            $table->integer('updated_by_user_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            
            // FOREIGN KEYS
            $table->foreign('parent_id')->references('id')->on('materials');
            $table->foreign('material_type_id')->references('id')->on('material_types');
            $table->foreign('from_orton_cone_id')->references('id')->on('orton_cones');
            $table->foreign('to_orton_cone_id')->references('id')->on('orton_cones');
            $table->foreign('surface_type_id')->references('id')->on('surface_types');
            $table->foreign('transparency_type_id')->references('id')->on('transparency_types');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('created_by_user_id')->references('id')->on('users');
            $table->foreign('updated_by_user_id')->references('id')->on('users');

        });

        DB::statement('ALTER TABLE `materials` ADD FULLTEXT INDEX `materials_fulltext` (`description`, `name`, `other_names`)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('materials');
    }
}
