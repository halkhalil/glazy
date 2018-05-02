<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\UserMaterialRepository;
use App\Models\Material;
use App\Models\MaterialAnalysis;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateusermaterials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user materials on May 1 2018.  One-time script';

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
        echo "Updating all user materials...\n";

        $userMaterialRepository = new UserMaterialRepository();

        //$query = UserMaterial::distinct()->get(['user_id']);
        $distinctUsers = DB::table('user_materials')->distinct()->select('user_id')->get();


        foreach ($distinctUsers as $distinctUser) {
            echo "USER: ".$distinctUser->user_id."\n";

            $user = User::with('user_materials')->find($distinctUser->user_id);

            if($user) {
                $materialIds = [];
                foreach (self::NEW_DEFAULT_MATERIALS as $material_id) {
                    if (!$user->user_materials) {
                        $materialIds[] = $material_id;
                    }
                    else {
                        $hasMaterial = false;
                        foreach ($user->user_materials as $user_material) {
                            if ($user_material->material_id === $material_id) {
                                $hasMaterial = true;
                                break;
                            }
                        }
                        if (!$hasMaterial) {
                            $materialIds[] = $material_id;
                        }
                    }
                }

                if (count($materialIds) > 0) {
                    echo print_r($materialIds);

                    $defaults = [];
                    foreach ($materialIds as $materialId) {
                        $defaults[] = [
                            'user_id' => $user->id,
                            'material_id' => $materialId
                        ];
                    }

                    DB::table('user_materials')->insert($defaults);

                    echo 'USER: '.$user->id." inserted new materials\n";
                }
                else {
                    echo "USER: ".$user->id." NO NEED TO UPDATE\n";
                }
            }

        }

        echo "Completed user materials update.\n";
    }


    const NEW_DEFAULT_MATERIALS = [
        // 2018/05/01 Add more:
        16050,    // Neph Sye A270
        15401,    // Silicon carbide
        15591,    // Calcined Talc, Magnesium Silicate
        15607,    // Vansil W-30 Wollastonite
        15352,    // Nickel Oxide
        15010,    // Tile #6
        15184,    // Frit 3417
        15020,    // Alumina Hydrate
        15047,    // Mixed wood ash
        15019,    // Alumina
        15067,    // Barnard Clay
        15258,    // Goldart
        15364,    // Petalite
        15307,    // Laguna Borate
        15468,    // Zirconium dioxide
        15384,    // Red clay
        15624,    // G200 HP Feldspar, G200HP
        15077,    // Black Iron Oxide
        15138,    // Epsom salts
        15016,    // Alberta Slip
        15423,    // Spodumene - Australian
        15500,    // Frit, Johnson Matthey 169
        15038,    // Hardwood ash
        15432,    // Superpax
        15096,    // Calcium borate frit
        15302,    // Kingman Feldspar
        15216,    // Frit P-25
        15175,    // Frit 3269
        15272,    // Hawthorne Bond
        15444,    // Ultrox
        15429,    // Standard borax frit
        15113,    // CMC
        15491,    // Veegum T
        15350,    // Newman Red Clay
        15329,    // Macaloid
        15482,    // Mason 6600 black stain
        15335,    // Manganese Carbonate
        15476,    // Feldspar FFF
        15460,    // XX Sagger Ball Clay
        15300,    // Kentucky Stone
        15130,    // Cryolite
        15265,    // Grog
        15220,    // Frit P-54
        15858,    // Hyplas 71 Ball clay
        15798,    // Ravenscrag Slip
        15439,    // Tennessee ball clay
        15750,    // Ferro Frit 3249
        15132,    // Darvan
        15275,    // Ilmenite
        15144,    // Fire clay (Theoretical)
        15474,    // C-6 Soda Feldspar
        15264,    // Green nickel oxide
        15287,    // K-200 Feldspar
        15348,    // NC-4 Feldspar
        15369,    // Plastic Vitrox
        15936,    // Mahavir Potash Feldspar
        15088,    // C & C  ball clay
        15395,    // Salt
        15891    // Custer Feldspar (2000-2012 Ron Roy)
    ];
}
