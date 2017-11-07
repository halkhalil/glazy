<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class GlazyTableSeeder
 *
 * First migrate the Glazy tables:
 * php artisan migrate --path="database/migrations/glazy/"
 *
 * Then:
 * php artisan db:seed
 *
 */
class GlazyTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
    {
        // command line: mysql -u homestead -psecret homestead < glaze_data_seeder.sql
        //DB::unprepared(file_get_contents('/Users/dau/code/glazyapi5/database/seeds/Glazy/glaze_data_seeder.sql'));
        exec("mysql -u ".Config::get('database.mysql.user')." -p".Config::get('database.mysql.password')." ".Config::get('database.mysql.database')." < /Users/dau/code/glazyapi5/database/seeds/Glazy/glaze_data_seeder.sql");

            /*
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $this->call(OrtonConeTableSeeder::class);
        $this->call(SurfaceTypeTableSeeder::class);
        $this->call(TransparencyTypeTableSeeder::class);
        $this->call(AtmosphereTableSeeder::class);
        $this->call(MaterialTypeTableSeeder::class);
        $this->call(CollectionTypeTableSeeder::class);
//        $this->call(MaterialSeeder::class); // USE SQL import
//        $this->call(UserTokenTableSeeder::class);  Use Passport instead

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
            */
// TODO:
//        DB::unprepared(file_get_contents('/Applications/MAMP/htdocs/glazyapi/database/seeds/Glazy/ImportOldGlazy.sql'));
    }
}