<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('username',50)->nullable(); // TODO: Drop this column

            // social login fields
            $table->string('facebook_id')->nullable();
            $table->string('facebook_avatar')->nullable();

            $table->string('google_id')->nullable();
            $table->string('google_avatar')->nullable();

            // various profile fields
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->string('location')->nullable();

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_filename')->nullable();
            $table->string('url')->nullable();
            $table->string('pinterest')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_profiles');
    }
}
