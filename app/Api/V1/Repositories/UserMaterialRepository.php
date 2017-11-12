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

    public function getByUserId()
    {
        $user_id =  Auth::user()->id;
        $userMaterials = UserMaterial::with('material')
            ->with('material.analysis')
            ->where('user_id', $user_id)
            ->get();

        if (!$userMaterials->count()) {
            $this->initializeUserMaterials();
            $userMaterials = UserMaterial::with('material')
                ->with('material.analysis')
                ->where('user_id', $user_id)
                ->get();
        }

        return $userMaterials;
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

        $materialQuery = Material::query();
        $materialQuery->whereIn('id', function($query) use ($user_id) {
            $query->selectRaw('material_id')
                ->from('user_materials')
                ->where('user_id', $user_id);
        });
        if ($id) {
            $materialQuery->orWhereIn('id', function($query) use ($id) {
                $query->selectRaw('component_material_id')
                    ->from('material_materials')
                    ->where('parent_material_id', $id);
            });
        }
        $materialQuery->with('analysis');
        $materialQuery->orderBy('name', 'asc');
        return $materialQuery->get();
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
        12400,    // Silica
        12457,    // Whiting
        12137,    // EPK
        12349,    // Nepheline Syenite (Theoretical)
        12072,    // Bentonite
        12135,    // Dolomite
        12387,    // Red iron oxide
        12288,    // Kaolin (Theoretical)
        12131,    // Custer Feldspar
        12252,    // Gerstley Borate
        12467,    // Zinc Oxide
        12433,    // Talc (Theoretical)
        12120,    // Copper Carbonate
        12169,    // Frit 3134
        12393,    // Rutile
        12458,    // Wollastonite (Theoretical)
        12323,    // Lithium Carbonate
        12442,    // Tin Oxide
        12114,    // Cobalt Carbonate
        12058,    // Ball Clay (Theoretical)
        12371,    // Potash Feldspar (Theoretical)
        12062,    // Barium Carbonate
        12431,    // Strontium Carbonate
        12443,    // Titanium Dioxide
        12298,    // Kentucky OM #4 Ball Clay
        12469,    // Zircopax
        12305,    // Kona F-4 feldspar
        12168,    // Frit 3124
        12117,    // Cobalt Oxide
        12080,    // Bone Ash
        12422,    // Spodumene (Theoretical)
        12108,    // Chrome Oxide
        12336,    // Manganese Dioxide
        12407,    // Soda Feldspar (Theoretical)
        12406,    // Soda Ash
        12122,    // Copper Oxide
        12167,    // Frit 3110
        12331,    // Magnesium carbonate
        12249,    // G-200 Feldspar
        12389,    // Redart
        12125,    // Cornwall Stone
        12657,    // Custer Feldspar (1989)
        12119,    // Colemanite
        12171,    // Frit 3195
        12268,    // Grolleg Kaolin
        12082,    // Borax
        12470,    // Minspar 200
        12094,    // Calcined Kaolin
        12253,    // Gillespie Borate
        12464,    // Yellow iron oxide
        12142,    // Feldspar
        12605,    // New Zealand Halloysite, Kaolin
        12055,    // Wood ash
        12015,    // Albany slip
        12465,    // Yellow Ochre
        12281,    // Iron oxide
        12594,    // Tricalcium Phosphate, Synthetic Bone Ash
        12483,    // Mason stain
        12637,    // Nepheline Syenite Norwegian
        12608    // Amtalc-C98 Talc
    ];

}
