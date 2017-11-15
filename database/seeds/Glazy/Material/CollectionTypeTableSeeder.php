<?php

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionTypeTableSeeder extends Seeder
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

        DB::table('collection_types')->truncate();

        $types = [
            [
                'id' => 1,
                'name' => 'User',
                'description' => 'A collection for only the user.',
            ],
            [
                'id' => 2,
                'name' => 'Group',
                'description' => 'A group collection.',
            ],
            [
                'id' => 3,
                'name' => 'World',
                'description' => 'A collection available to everyone.',
            ],
            [
                'id' => 4,
                'name' => 'Bookmarks',
                'description' => 'Personal User Bookmarks',
            ]
        ];

        DB::table('collection_types')->insert($types);

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}
