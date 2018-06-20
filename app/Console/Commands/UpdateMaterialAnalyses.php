<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\MaterialRepository;
use App\Models\Material;
use App\Models\MaterialAnalysis;
use Illuminate\Console\Command;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\PercentageAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\FormulaAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Material\PrimitiveMaterial;

class UpdateMaterialAnalyses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updatematerialanalyses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update material analyses';

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
        echo "Updating all primitive material analyses...\n";

        $materialRepository = new MaterialRepository();

        $query = Material::query();
        $query->with('analysis');
        $query->where(function ($query) {
            $query->where('is_analysis', true)
                ->orWhere('is_primitive', true);
        });
        $query->whereHas('analysis', function($query) {
            $query->whereNull('SiO2_mol')
                ->whereNull('Al2O3_mol')
                ->whereNull('CaO_mol');
        });
        $query->where('created_by_user_id', 1);
        echo "Entering while loop...\n";

        $loop_limit = 100;
        $query_limit = 500;
//        $loop_limit = 1;
//        $query_limit = 1;
        $sleep = 1;

        for ($i = 1; $i <= $loop_limit; $i++) {
            echo " Querying DB ".$i.": ".$query->toSql()."\n";
            $materials = $query->paginate($query_limit, ['*'], 'page', $i);

            echo "DB Queried...\n";

            if (!$materials)
            {
                echo "No materials found with null mol percent analysis.\n";
                break;
            }

            echo "Materials found: ".$materials->count()."\n";

            foreach($materials as $material) {
                echo "Updating: ".$material->id." ".$material->name."\n";

                if ($material->analysis) {
                    $analysis = $material->analysis;
                    echo "Old Analysis Percent: SiO2 ".$analysis->SiO2_percent." Al2O3 ".$analysis->Al2O3_percent."\n";

                    $percentageAnalysis = $analysis->getPercentageAnalysis();
                    $ceramicscalcMaterial = new PrimitiveMaterial($material->id);
                    $ceramicscalcMaterial->setPercentageAnalysis($percentageAnalysis, FormulaAnalysis::UNITY_TYPE_AUTO);
                    $analysis->setAnalysis($ceramicscalcMaterial);
                    $analysis->save();

                    echo "New Analysis Percent: SiO2 ".$analysis->SiO2_percent." Al2O3 ".$analysis->Al2O3_percent."\n";
                }
            }

            echo "Sleeping for a bit...\n";
            sleep($sleep); // Delay the script execution for 2 seconds
        }

        echo "Completed materials update.\n";
    }
}
