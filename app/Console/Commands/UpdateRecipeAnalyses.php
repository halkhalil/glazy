<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\MaterialMaterialRepository;
use App\Models\Material;
use App\Models\MaterialAnalysis;
use Illuminate\Console\Command;

class UpdateRecipeAnalyses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updaterecipeanalyses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update recipe analyses after import of old db';

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
//        $query->where('id', '>', 3900);
/*
        $query->whereHas('analysis', function($query)
        {
            $query->whereNull('SiO2_percent');
            $query->whereNull('Al2O3_percent');

        });
*/
        echo "Entering while loop...\n";

        $loop_limit = 100;
        $query_limit = 100;
        $sleep = 1;

        for ($i = 1; $i <= $loop_limit; $i++) {
            echo " Querying DB ".$i.": ".$query->toSql()."\n";

//            $materials = $query->limit($query_limit)->get();
            $materials = $query->paginate($query_limit, ['*'], 'page', $i);

            echo "DB Queried...\n";

            if (!$materials)
            {
                echo "No materials found with null percent analysis.\n";
                break;
            }

            echo "Materials found: ".$materials->count()."\n";

            foreach($materials as $material) {
                echo "Updating: ".$material->id." ".$material->name."\n";

                if ($material->analysis) {

                    $analysis = $material->analysis;

                    echo "Old Analysis UMF: SiO2 ".$analysis->SiO2_umf." Al2O3 ".$analysis->Al2O3_umf."\n";
//                    echo "OLD LOI: ".$analysis->loi."\n";
                    $materialMaterialRepository->updateAnalysis($material);

                    echo "New Analysis UMF: SiO2 ".$analysis->SiO2_umf." Al2O3 ".$analysis->Al2O3_umf."\n";
//                    echo "NEW LOI: ".$analysis->loi."\n";

                }
            }

            echo "Sleeping for a bit...\n";
            sleep($sleep); // Delay the script execution for 2 seconds
        }

        echo "Completed materials update.\n";
    }
}
