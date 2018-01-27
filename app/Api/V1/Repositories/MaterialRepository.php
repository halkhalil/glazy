<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\CollectionMaterial;
use App\Models\Collection;
use App\Models\Material;
use App\Models\MaterialAtmosphere;
use App\Models\MaterialImage;
use App\Models\MaterialMaterial;
use App\Models\MaterialReview;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\PercentageAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Material\PrimitiveMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialRepository extends Repository
{
    public function getModel()
    {
        return new Material();
    }

    public function get($id)
    {
        // return Material::with('analysis')
        //     ->where('is_primitive', false)->find($id);
        return Material::with('analysis')->find($id);
    }

    public function getWithDetails($id)
    {
        return Material::with('analysis')
            ->with('components')
            ->with('thumbnail')
            ->with('thumbnail.created_by_user')
            ->with('images')
            ->with('reviews')
            ->with('created_by_user')
            ->with('created_by_user.profile')
            // ->where('is_primitive', false)
            ->find($id);
    }

    public function getAll()
    {
        return Material::with('analysis')->where('is_primitive', false)->get();
    }

    public function create(array $data)
    {
        $material = $this->getModel();
        
        $material->fill($data);

        // TODO: might need to change later for primitive creation
        $material->is_primitive = false;

        // Set this recipe's owner
        $material->created_by_user_id = Auth::guard()->user()->id;

        // All copied recipes are private by default
        $material->is_private = true;

        $material->save();

        return $material;
    }

    public function update(Model $material, array $jsonData)
    {
        if ($material->is_archived) {
            // Archived materials cannot be updated
            return false;
        }

        $data = [];
        $data['id'] = $jsonData['id'];
        $data['name'] = $jsonData['name'];
        if (array_key_exists('otherNames', $jsonData)) {
            if ($jsonData['otherNames']) {
                $data['other_names'] = $jsonData['otherNames'];
            } else {
                $data['other_names'] = null;
            }
        }
        if (array_key_exists('description', $jsonData)) {
            $data['description'] = $jsonData['description'];
        }
        if (array_key_exists('baseTypeId', $jsonData)) {
            if ($jsonData['baseTypeId']) {
                $data['base_type_id'] = $jsonData['baseTypeId'];
            } else {
                $data['base_type_id'] = null;
            }
        }
        if (array_key_exists('materialTypeId', $jsonData)) {
            if ($jsonData['materialTypeId']) {
                $data['material_type_id'] = $jsonData['materialTypeId'];
            } else {
                $data['material_type_id'] = null;
            }
        }
        if (array_key_exists('transparencyTypeId', $jsonData)) {
            if ($jsonData['transparencyTypeId']) {
                $data['transparency_type_id'] = $jsonData['transparencyTypeId'];
            } else {
                $data['transparency_type_id'] = null;
            }
        }
        if (array_key_exists('surfaceTypeId', $jsonData)) {
            if ($jsonData['surfaceTypeId']) {
                $data['surface_type_id'] = $jsonData['surfaceTypeId'];
            } else {
                $data['surface_type_id'] = null;
            }
        }
        if (array_key_exists('countryId', $jsonData)) {
            if ($jsonData['countryId']) {
                $data['country_id'] = $jsonData['countryId'];
            } else {
                $data['country_id'] = null;
            }
        }
        if (array_key_exists('fromOrtonConeId', $jsonData)) {
            $data['from_orton_cone_id'] = $jsonData['fromOrtonConeId'];
        }
        if (array_key_exists('toOrtonConeId', $jsonData)) {
            $data['to_orton_cone_id'] = $jsonData['toOrtonConeId'];
        }
        if (array_key_exists('atmospheres', $jsonData)) {
            $data['atmospheres'] = $jsonData['atmospheres'];
        }

        $material->fill($data);
        if (isset($data['hex_color'])) {
//            Log::error("HEX COLOR: ".$data['hex_color']);
            $hex_color = $data['hex_color'];
            $material->setRGBFromHex($hex_color);
        }

        $material->save();

        // First, remove existing atmospheres
        $deletedRows = MaterialAtmosphere::where('material_id', $material->id)->delete();

        // Now add atmospheres
        if (isset($data['atmospheres'])) {
            foreach ($data['atmospheres'] as $atmosphere_id)
            {
                $atmosphere = new MaterialAtmosphere();
                $atmosphere->material_id = $material->id;
                $atmosphere->atmosphere_id = $atmosphere_id;
                $atmosphere->save();
            }
        }

        if ($material->is_primitive && $jsonData['analysis']) {
            // For primitive materials, we also update the analysis
            $percentageAnalysis = new PercentageAnalysis();
            $percentageAnalysis->setOxides($jsonData['analysis']);
            $percentageAnalysis->setLOI($jsonData['loi']);
            $primitiveMaterial = new PrimitiveMaterial($material->id);
            $primitiveMaterial->setPercentageAnalysis($percentageAnalysis);
            $material->analysis->setAnalysis($primitiveMaterial);

            if (array_key_exists('weight', $jsonData)) {
                $material->analysis['weight'] = $jsonData['weight'];
            }

            $material->analysis->save();
        }

        return $material;
    }



    public function destroy($id)
    {
        $material = Material::with('analysis')->with('thumbnail')->find($id);

        if (!$material) {
            return false;
        }

        if ($material->is_archived) {
            // Archived materials cannot be deleted
            return false;
        }

        if ($material->analysis) {
            $material->analysis->delete();
        }

        // This material's images may also be referenced in the collections as
        // the collection thumbnail_id.  Set these to null, first..
        $collectionsQuery = Collection::query();
        $collectionsQuery->whereIn('thumbnail_id', function($query) use ($id) {
            $query->selectRaw('id')
                ->from('material_images')
                ->where('material_id', $id);
        });
        $collections = $collectionsQuery->get();
        foreach ($collections as $collection) {
            $collection->thumbnail_id = null;
            $collection->timestamps = false;
            $collection->save();
        }

        if ($material->thumbnail_id) {
            // Unset any reference to the material_images table:
            $material->thumbnail_id = null;
            $material->save();
        }

        $deletedRows = MaterialMaterial::where('parent_material_id', $id)->delete();
        $deletedRows = MaterialAtmosphere::where('material_id', $id)->delete();
        $deletedRows = MaterialImage::where('material_id', $id)->delete();
        $deletedRows = MaterialReview::where('material_id', $id)->delete();
        $deletedRows = CollectionMaterial::where('material_id', $id)->delete();

        $material->delete();

        return true;
    }


    public function copy(Model $material)
    {
        $copiedMaterial = $material->replicate();

        // Give this duplicate a new name
        $copiedMaterial->name = $copiedMaterial->name.' (Copy)';

        // Set this recipe's owner
        $copiedMaterial->created_by_user_id = Auth::user()->id;

        // We're not copying over images, so remove thumbnail reference.
        $copiedMaterial->thumbnail_id = null;

        // Remove update user
        $copiedMaterial->updated_by_user_id = null;

        // Remove any rating information
        $copiedMaterial->rating_total = 0;
        $copiedMaterial->rating_number = 0;
        $copiedMaterial->rating_average = 0;

        // All copied recipes are private by default
        $copiedMaterial->is_private = true;

        $copiedMaterial->save();

        // Copy the analysis
        $analysis = $material->analysis;
        $copiedAnalysis = $analysis->replicate();
        $copiedAnalysis->id = null;
        $copiedAnalysis->material_id = $copiedMaterial->id;
        $copiedAnalysis->save();

        if (!$copiedMaterial->is_primitive) {
            $componentMaterials = MaterialMaterial::where('parent_material_id', $material->id)->get();
            foreach ($componentMaterials as $componentMaterial)
            {
                $copiedComponent = $componentMaterial->replicate();
                $copiedComponent->parent_material_id = $copiedMaterial->id;
                $copiedComponent->save();
            }
        }

        $materialAtmospheres = MaterialAtmosphere::where('material_id', $material->id)->get();
        foreach($materialAtmospheres as $materialAtmosphere)
        {
            $copiedAtmosphere = $materialAtmosphere->replicate();
            $copiedAtmosphere->material_id = $copiedMaterial->id;
            $copiedAtmosphere->save();
        }

        return $copiedMaterial;
    }

    /*
        http://stackoverflow.com/questions/3013695/mysql-query-to-obtain-recipes-using-all-given-ingredients
        SELECT *
        FROM materials
        WHERE EXISTS (SELECT 1
                      FROM material_materials
                      WHERE material_materials.parent_material_id = materials.id AND
                            material_materials.component_material_id = 10249 AND
                            material_materials.percentage_amount = 38.9) AND
              EXISTS (SELECT 1
                          FROM material_materials
                          WHERE material_materials.parent_material_id = materials.id AND
                                material_materials.component_material_id = 10457 AND
                            material_materials.percentage_amount = 16.7);

    */
    public function similarMaterials(array $data) {

        $excludeMaterialId = null;
        if (array_key_exists('excludeMaterialId', $data)) {
            $excludeMaterialId = $data['excludeMaterialId'];
        }
        $componentData = $data['materialComponents'];
        Log::error('POST MAT COMP: '.print_r($componentData, true));

        if (count($componentData) < 1) {
            return null;
        }

        $query = Material::query();
        $query->with('analysis');
        $query->with('atmospheres');
        $query->with('material_type');
        $query->with('thumbnail');
        $query->with('created_by_user');
        $query->with('created_by_user.profile');

        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }

        $query->ofUserViewable($current_user_id, null);

        if ($excludeMaterialId) {
            $query->where('id', '<>', $excludeMaterialId);
        }

        foreach ($componentData as $componentMaterialData) {
            if (isset($componentMaterialData['componentMaterialId'])
                && isset($componentMaterialData['percentageAmount'])
                && $componentMaterialData['percentageAmount'] > 0
            ) {

                $childId = $componentMaterialData['componentMaterialId'];
                $amount = $componentMaterialData['percentageAmount'];
                Log::error('ADD: '.$childId.' amount '.$amount);

                $query->whereExists(function ($query) use ($childId, $amount) {
                    $query->select(DB::raw(1))
                        ->from('material_materials')
                        ->whereRaw('material_materials.parent_material_id = materials.id')
                        ->whereRaw('material_materials.component_material_id = '.$childId)
                        ->whereRaw('material_materials.percentage_amount = '.$amount);
                });
            }
        }

        $query->orderBy('updated_at', 'DESC');

        return $query->limit(20)->get();
    }

    /*
     * Stupid!  Checking hashes won't work
    */
    /*
    public function similarMaterials(array $data) {

        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }

        $excludeMaterialId = null;
        if (array_key_exists('excludeMaterialId', $data)) {
            $excludeMaterialId = $data['excludeMaterialId'];
        }
        $componentData = $data['materialComponents'];
        Log::error('POST MAT COMP: '.print_r($componentData, true));

        if (count($componentData) < 1) {
            return null;
        }

        $materialIds = []
        foreach ($componentData as $componentMaterialData) {
            if (isset($componentMaterialData['componentMaterialId'])
                && isset($componentMaterialData['percentageAmount'])
                && $componentMaterialData['percentageAmount'] > 0) {
                $materialIds[] = $componentMaterialData['componentMaterialId'];
            }
        }

        $materials = Materia::whereIn('id', $materialIds)->get();
        $parentMaterialIds = [];
        foreach ($materials as $material) {
            $parentMaterialIds[$material->id] = $material->parent_id;
        }

        $material = [];
        foreach ($componentData as $componentMaterialData) {
            if (isset($componentMaterialData['componentMaterialId'])
                && isset($componentMaterialData['percentageAmount'])
                && $componentMaterialData['percentageAmount'] > 0
            ) {
                $material['components'][] = [
                    'component_material_id' => $componentMaterialData['componentMaterialId'],
                    'component_material' => [
                        'is_primitive' => true,
                        'parent_id' => $parentMaterialIds[$componentMaterialData['componentMaterialId']]
                    ],
                    'percentage_amount' => $componentMaterialData['percentageAmount'],
                    'is_additional' => $componentMaterialData['isAdditional']
                ];
            }
        }

        $materialMaterialRepository = new MaterialMaterialRepository();
        $materialMaterialRepository->setComponentHashes($material);

        if (!$material->additive_composite_hash) {
            return null;
        }

        // Query the database for similar recipes (including additives
        $query = Material::query();
        $query->with('analysis');
        $query->with('atmospheres');
        $query->with('material_type');
        $query->with('thumbnail');
        $query->with('created_by_user');
        $query->ofUserViewable($current_user_id, null);
        if ($excludeMaterialId) {
            // Exclude the current recipe
            $query->where('id', '<>', $excludeMaterialId);
        }
        $query->where('additive_composite_hash', $material->base_composite_hash);
        $query->orderBy('updated_at', 'DESC');

        return $query->limit(20)->get();
    }
    */

    public function similarUnityFormula($material_id)
    {
        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }

        $query = Material::query();
        $query->ofUserViewable($current_user_id, null);

        $material = $query->where('id', $material_id)->first();

        if (!$material || empty($material->base_composite_hash)) {
            // Either material doesn't exist, cannot be viewed,
            // or has no materials added to its hash
            return null;
        }

        $query = Material::query();

        $query->join('material_analyses as analyses', 'analyses.material_id', '=', 'materials.id');

        $Al2O3_umf = $material->analysis->Al2O3_umf;
        $SiO2_umf = $material->analysis->SiO2_umf;
        $R2O_umf = $material->analysis->R2O_umf;

        if ($Al2O3_umf === null) { $Al2O3_umf = 0; }
        if ($SiO2_umf === null) { $SiO2_umf = 0; }
        if ($R2O_umf === null) { $R2O_umf = 0; }

        $distanceField =
            '('.$Al2O3_umf.' - analyses.Al2O3_umf) * ('.$Al2O3_umf.' - analyses.Al2O3_umf) + '
            .'('.$SiO2_umf.' - analyses.SiO2_umf) * ('.$SiO2_umf.' - analyses.SiO2_umf) + '
            .'('.$R2O_umf.' - analyses.R2O_umf) * ('.$R2O_umf.' - analyses.R2O_umf)';

        $selectFields = 'materials.id, materials.name, materials.is_primitive, materials.material_type_id, '
            .'materials.is_analysis, materials.is_theoretical, materials.from_orton_cone_id, '
            .'materials.to_orton_cone_id, materials.surface_type_id, materials.transparency_type_id, '
            .'materials.thumbnail_id, materials.is_private, materials.is_archived, materials.created_by_user_id, '
            .'materials.created_at, materials.updated_at, '
            .$distanceField.' as distance';

        $query->select(DB::raw($selectFields));

        $query->with('material_type');
        $query->with('analysis');
        $query->with('thumbnail');
        $query->with('created_by_user');
        $query->with('created_by_user.profile');

        $query->ofUserViewable($current_user_id, null);

        if ($material_id) {
            // Exclude the current recipe
            $query->where('materials.id', '<>', $material_id);
        }

        $variance = .05;
        $sial_variance = .5;

        // ORDER WHERE CLAUSES BY PREVALENCE OF OXIDE
        $query->whereRaw('SiO2_Al2O3_ratio_umf BETWEEN ? AND ?', [$material->analysis->SiO2_Al2O3_ratio_umf - $sial_variance, $material->analysis->SiO2_Al2O3_ratio_umf + $sial_variance]);

        $query->whereRaw('SiO2_umf BETWEEN ? AND ?', [$material->analysis->SiO2_umf - $variance, $material->analysis->SiO2_umf + $variance]);
        $query->whereRaw('Al2O3_umf BETWEEN ? AND ?', [$material->analysis->Al2O3_umf - $variance, $material->analysis->Al2O3_umf + $variance]);
        $query->whereRaw('B2O3_umf BETWEEN ? AND ?', [$material->analysis->B2O3_umf - $variance, $material->analysis->B2O3_umf + $variance]);
        $query->whereRaw('Li2O_umf BETWEEN ? AND ?', [$material->analysis->Li2O_umf - $variance, $material->analysis->Li2O_umf + $variance]);
        $query->whereRaw('KNaO_umf BETWEEN ? AND ?', [$material->analysis->KNaO_umf - $variance, $material->analysis->KNaO_umf + $variance]);
        $query->whereRaw('MgO_umf BETWEEN ? AND ?', [$material->analysis->MgO_umf - $variance, $material->analysis->MgO_umf + $variance]);
        $query->whereRaw('CaO_umf BETWEEN ? AND ?', [$material->analysis->CaO_umf - $variance, $material->analysis->CaO_umf + $variance]);
        $query->whereRaw('SrO_umf BETWEEN ? AND ?', [$material->analysis->SrO_umf - $variance, $material->analysis->SrO_umf + $variance]);
        $query->whereRaw('BaO_umf BETWEEN ? AND ?', [$material->analysis->BaO_umf - $variance, $material->analysis->BaO_umf + $variance]);

        $query->orderBy('distance', 'ASC');

        return $query->limit(100)->get();

    }

    public function similarBaseComponents($material_id)
    {
        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }

        $query = Material::query();
        $query->ofUserViewable($current_user_id, null);

        $material = $query->where('id', $material_id)->first();

        if (!$material || empty($material->base_composite_hash)) {
            // Either material doesn't exist, cannot be viewed,
            // or has no materials added to its hash
            return null;
        }

        $query = Material::query();

        $query->select(
            'id', 'name',
            'is_primitive', 'material_type_id',
            'is_analysis', 'is_theoretical',
            'from_orton_cone_id', 'to_orton_cone_id',
            'surface_type_id', 'transparency_type_id',
            'rating_total', 'rating_number',
            'rgb_r', 'rgb_g', 'rgb_b', 'thumbnail_id',
            'is_private', 'is_archived',
            'created_by_user_id', 'updated_by_user_id',
            'created_at', 'updated_at'
        );
        $query->with('thumbnail');
        $query->with('created_by_user');
        $query->with('created_by_user.profile');

        $query->ofUserViewable($current_user_id, null);

        if ($material_id) {
            // Exclude the current recipe
            $query->where('id', '<>', $material_id);
        }

        $query->where('base_composite_hash', $material->base_composite_hash);

        $query->orderBy('updated_at', 'DESC');

        return $query->limit(100)->get();

    }

}
