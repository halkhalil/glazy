<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\MaterialRepository;
use App\Models\Material;
use Illuminate\Console\Command;

class DeletePrivateMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteprivatematerials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CAUTION: Delete ALL private materials from database';

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
        echo "Deleting all private recipes...\n";

        $materialRepository = new MaterialRepository();

        $query = Material::query();
        $query->select('id', 'name');
        $query->where('is_private', true);
        $query->where('is_primitive', false);
        $query->where('deleted_at', null);
        $query->where('created_by_user_id', '<>', 1); // Exclude admin recipes
        $query->where('created_by_user_id', '<>', 7); // Exclude derek's recipes

        $privateMaterials = $query->get();

        echo "Private materials found: ".$privateMaterials->count()."\n";

        foreach($privateMaterials as $material) {
            echo "Delete: ".$material->id." ".$material->name."\n";
            $materialRepository->destroy($material->id);
        }

        echo "Completed delete private recipes.\n";
    }
}
