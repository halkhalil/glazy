<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('last_name')->nullable()->after('name');
            $table->string('first_name')->nullable()->after('name');

            $table->timestamp('last_login')->nullable()->after('remember_token');

            $table->string('api_token', 60)->unique()->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            $table->dropColumn('api_token');
            $table->dropColumn('last_login');

        });

    }
}
