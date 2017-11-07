<?php

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('materials')->truncate();



        $recipes = [
            [
                'id' =>  1,
                'old_id' => 400,
                'name' =>  'Silica',
                'search_words' => 'Silica, Flint, Quartz',
                'material_type_id' =>  32,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,
            ],
            [
                'id' =>  2,
                'old_id' => 371,
                'name' =>  'Potash Feldspar',
                'search_words' => 'Potash Feldspar',
                'material_type_id' =>  20,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  3,
                'old_id' => 137,
                'name' =>  'EPK',
                'search_words' => 'EPK, EP Kaolin, Edgar Plastic',
                'material_type_id' =>  13,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  4,
                'old_id' => 387,
                'name' =>  'Red Iron Oxide',
                'search_words' => 'Red iron oxide, RIO',
                'material_type_id' =>  17,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  5,
                'old_id' => 268,
                'name' =>  'Grolleg Kaolin',
                'search_words' => 'Grolleg Kaolin',
                'material_type_id' =>  13,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  6,
                'old_id' => 114,
                'name' =>  'Cobalt Carbonate',
                'search_words' => 'Cobalt Carbonate',
                'material_type_id' =>  17,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  7,
                'old_id' => 458,
                'name' =>  'Wollastonite',
                'search_words' => 'Wollastonite',
                'material_type_id' =>  23,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,

            ],
            [
                'id' =>  8,
                'old_id' => 457,
                'name' =>  'Whiting',
                'search_words' => 'Whiting, Calcium Carbonate',
                'material_type_id' =>  23,
                'is_primitive' =>  true,
                'is_private' =>  false,
                'created_by_user_id' =>  1,
            ],
        ];

        DB::table('materials')->insert($recipes);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}
