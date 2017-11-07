<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrtonConesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orton_cones', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name',12);
            $table->text('description')->nullable();
            $table->string('hex_color',6)->nullable();
            $table->smallInteger('F_R_27')->default(0);
            $table->smallInteger('F_R_108')->default(0);
            $table->smallInteger('F_R_270')->default(0);
            $table->smallInteger('F_R_IF_27')->default(0);
            $table->smallInteger('F_R_IF_108')->default(0);
            $table->smallInteger('F_R_IF_270')->default(0);
            $table->smallInteger('F_L_108')->default(0);
            $table->smallInteger('F_L_270')->default(0);
            $table->smallInteger('F_L_IF_108')->default(0);
            $table->smallInteger('F_L_IF_270')->default(0);
            $table->smallInteger('F_S_540')->default(0);
            $table->smallInteger('C_R_15')->default(0);
            $table->smallInteger('C_R_60')->default(0);
            $table->smallInteger('C_R_150')->default(0);
            $table->smallInteger('C_R_IF_15')->default(0);
            $table->smallInteger('C_R_IF_60')->default(0);
            $table->smallInteger('C_R_IF_150')->default(0);
            $table->smallInteger('C_L_60')->default(0);
            $table->smallInteger('C_L_150')->default(0);
            $table->smallInteger('C_L_IF_60')->default(0);
            $table->smallInteger('C_L_IF_150')->default(0);
            $table->smallInteger('C_S_300')->default(0);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orton_cones');
    }
}
