<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\UserMaterial;
use App\Models\Material;
use App\Models\MaterialMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserMaterialRepository extends Repository
{
    public function getModel()
    {
        return new UserMaterial();
    }

    public function get($id)
    {
        return UserMaterial::find($id);
    }

    public function getWithDetails($id)
    {
        return UserMaterial::with('material')->with('user')->find($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     *
     * Get all the materials in this user's inventory.
     * Need a join with materials table in order to sort by material name.
     */
    public function getByUserId()
    {
        $user_id =  Auth::user()->id;

        $query = UserMaterial::query();

        $query->join('materials as mat', 'mat.id', '=', 'user_materials.material_id')
            ->select('user_materials.*')
            ->with('material')
            ->with('material.analysis')
            ->with('material.material_type')
            ->with('material.thumbnail')
            ->with('material.created_by_user')
            ->with('material.created_by_user.profile')
            ->where('user_id', $user_id)
            ->orderBy('mat.name', 'ASC');

        $userMaterials = $query->get();

        if (!$userMaterials->count()) {
            $this->initializeUserMaterials();
            $userMaterials = $query->get();
        }

        return $userMaterials;
    }

    public function getUnauthenticatedMaterialList()
    {
        $query = Material::query();
        $query->whereIn('id', self::DEFAULT_MATERIALS);
        $query->with('analysis');
        $query->with('material_type');
        $query->orderBy('name', 'asc');
        return $query->get();
    }

    /**
     * @param null $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     *
     *
     */
    public function getEditMaterialList($id = null)
    {
        $user_id =  Auth::user()->id;

        // TODO: optimize, inneficient to query twice
        // Check if user has materials
        // (New users who have never edited before will not.)
        $userMaterialsCount = UserMaterial::where('user_id', $user_id)->count();

        if (!$userMaterialsCount) {
            $this->initializeUserMaterials();
        }

        $query = Material::query();
        $query->whereIn('id', function($query) use ($user_id) {
            $query->selectRaw('material_id')
                ->from('user_materials')
                ->where('user_id', $user_id);
        });
        if ($id) {
            $query->orWhereIn('id', function($query) use ($id) {
                $query->selectRaw('component_material_id')
                    ->from('material_materials')
                    ->where('parent_material_id', $id);
            });
        }
        $query->with('analysis');
        $query->with('material_type');
        $query->orderBy('name', 'asc');
        return $query->get();
    }

    /*
     * This is the first time a user has tried to get a list of his/her materials
     * Pre-populate this list using most common materials as well as
     * user's own materials
     * See DEFAULT_MATERIALS below
     * TODO: Move these default materials into DB or config
     */
    protected function initializeUserMaterials() {
        $user_id =  Auth::user()->id;

        // Get user's existing materials
        $existingUserPrimitiveMaterials = Material::where('is_primitive', true)
            ->where('created_by_user_id', $user_id)
            ->select('id')
            ->get();

        $materialIds = [];
        foreach ($existingUserPrimitiveMaterials as $matid) {
            $materialIds[] = $matid->id;
        }

        // Merge user material ID's with system defaults
        $materialIds = array_merge($materialIds, self::DEFAULT_MATERIALS);

        $defaults = [];
        foreach ($materialIds as $materialId) {
            $defaults[] = [
                'user_id' => $user_id,
                'material_id' => $materialId
            ];
        }
        DB::table('user_materials')->insert($defaults);
    }

    public function create(array $data)
    {
        $material_id = $data['material_id'];
        $user_id =  Auth::user()->id;

        $material = Material::find($material_id);

        if (!$material) {
            return false;
        }

        $userMaterial = $this->getModel();

        $userMaterial->fill($data);

        $userMaterial->material_id = $material->id;
        $userMaterial->user_id = $user_id;

        $userMaterial->save();

        return $userMaterial;
    }

    /**
     * @param $materialId
     * @return UserMaterial
     *
     * Simply add a material to the current user's inventory without
     * worring about stock amount, price, etc.
     */
    public function addMaterial($materialId) {
        // (Cannot use firstOrCreate because fields not mass-fillable.)
        $userMaterial = UserMaterial::where('user_id', Auth::guard('api')->user()->id)
            ->where('material_id', $materialId)
            ->first();

        if (!$userMaterial) {
            // No previous record exists.  Add one:
            $userMaterial = new UserMaterial();
            $userMaterial->user_id = Auth::guard('api')->user()->id;
            $userMaterial->material_id = $materialId;    
            $userMaterial->save();
        }            

        return $userMaterial;
    }

    public function update(Model $userMaterial, array $data)
    {
        $material = Material::find($userMaterial->material_id);

        if (!$material) {
            return false;
        }

        $userMaterial->fill($data);

        $userMaterial->save();

        return $userMaterial;
    }

    public function destroy(Model $userMaterial)
    {
        $userMaterial->delete();
    }

    const DEFAULT_MATERIALS = [
        15400,    // Silica
        15457,    // Whiting
        15137,    // EPK
        15349,    // Nepheline Syenite (Theoretical)
        15072,    // Bentonite
        15135,    // Dolomite
        15387,    // Red iron oxide
        15288,    // Kaolin (Theoretical)
        15131,    // Custer Feldspar
        15252,    // Gerstley Borate
        15467,    // Zinc Oxide
        15433,    // Talc (Theoretical)
        15120,    // Copper Carbonate
        15169,    // Frit 3134
        15393,    // Rutile
        15458,    // Wollastonite (Theoretical)
        15323,    // Lithium Carbonate
        15442,    // Tin Oxide
        15114,    // Cobalt Carbonate
        15058,    // Ball Clay (Theoretical)
        15371,    // Potash Feldspar (Theoretical)
        15062,    // Barium Carbonate
        15431,    // Strontium Carbonate
        15443,    // Titanium Dioxide
        15298,    // Kentucky OM #4 Ball Clay
        15469,    // Zircopax
        15305,    // Kona F-4 feldspar
        15168,    // Frit 3124
        15117,    // Cobalt Oxide
        15080,    // Bone Ash
        15422,    // Spodumene (Theoretical)
        15108,    // Chrome Oxide
        15336,    // Manganese Dioxide
        15407,    // Soda Feldspar (Theoretical)
        15406,    // Soda Ash
        15122,    // Copper Oxide
        15167,    // Frit 3110
        15331,    // Magnesium carbonate
        15249,    // G-200 Feldspar
        15389,    // Redart
        15125,    // Cornwall Stone
        15657,    // Custer Feldspar (1989)
        15119,    // Colemanite
        15171,    // Frit 3195
        15268,    // Grolleg Kaolin
        15082,    // Borax
        15470,    // Minspar 200
        15094,    // Calcined Kaolin
        15253,    // Gillespie Borate
        15464,    // Yellow iron oxide
        15142,    // Feldspar
        15605,    // New Zealand Halloysite, Kaolin
        15055,    // Wood ash
        15015,    // Albany slip
        15465,    // Yellow Ochre
        15281,    // Iron oxide
        15594,    // Tricalcium Phosphate, Synthetic Bone Ash
        15483,    // Mason stain
        15637,    // Nepheline Syenite Norwegian
        15608,    // Amtalc-C98 Talc

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
