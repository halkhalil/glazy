<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialHazardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_hazards', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('material_id')->unsigned()->unique();

            $table->char('hmis_health', 2)->nullable();
            $table->char('hmis_flammability', 2)->nullable();
            $table->char('hmis_physical_hazard', 2)->nullable();
            $table->char('hmis_personal_protection', 2)->nullable();
            $table->string('ghs')->nullable();
            $table->string('ghs_signal_word')->nullable();
            $table->text('ghs_statements')->nullable();
            $table->string('sds_url')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // FOREIGN KEYS
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
        Schema::drop('material_hazards');
    }
}
