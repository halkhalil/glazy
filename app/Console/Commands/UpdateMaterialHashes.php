<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\MaterialMaterialRepository;
use App\Models\Material;
use Illuminate\Console\Command;

class UpdateMaterialHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updatematerialhashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update hashes after import of old db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        echo "Updating all composite material analyses...\n";

        $materialMaterialRepository = new MaterialMaterialRepository();

        $query = Material::query();
        $query->with('components');
        $query->with('analysis');
        $query->where('is_analysis', false);
        $query->where('is_primitive', false);
        $query->where('base_composite_hash', '=', null);

        echo "Entering while loop...\n";

        $loop_limit = 1000;
        $query_limit = 100;
        $sleep = 1;

        for ($i = 1; $i <= $loop_limit; $i++) {
            echo " Querying DB ".$i.": ".$query->toSql()."\n";

//            $materials = $query->limit($query_limit)->get();
            $materials = $query->paginate($query_limit, ['*'], 'page', $i);

            echo "DB Queried...\n";

            if (!$materials || !$materials->count())
            {
                echo "No materials found with null percent analysis.\n";
                break;
            }

            echo "Materials found: ".$materials->count()."\n";

            foreach($materials as $material) {
                echo "Updating: ".$material->id." ".$material->name."\n";
                echo "Old base_composite_hash: ".$material->base_composite_hash." additive: ".$material->additive_composite_hash."\n";
                $materialMaterialRepository->setComponentHashes($material);
                $material->timestamps = false;
                $material->save();
                echo "New base_composite_hash: ".$material->base_composite_hash." additive: ".$material->additive_composite_hash."\n";
            }

            echo "Sleeping for a bit...\n";
            sleep($sleep); // Delay the script execution for 2 seconds
        }

        echo "Completed materials update.\n";
    }
}
