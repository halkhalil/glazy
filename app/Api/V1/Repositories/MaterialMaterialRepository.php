<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\MaterialAnalysis;
use App\Models\MaterialMaterial;

use DerekPhilipAu\Ceramicscalc\Models\Material\CompositeMaterial;

use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MaterialMaterialRepository extends Repository
{
    public function getModel()
    {
        return new MaterialMaterial();
    }

    public function getByParent($id)
    {
        return MaterialMaterial::where('parent_material_id', $id)->get();
    }

    public function getByParentWithDetails($id)
    {
        return MaterialMaterial::with('parent_material')->with('component_material')->where('parent_material_id', $id)->get();
    }

    public function updateAll($parentId, array $data)
    {

        $componentData = $data['materialComponents'];

        // First, remove existing child materials
        $deletedRows = MaterialMaterial::where('parent_material_id', $parentId)->delete();


        // Compact materials
        // Two entries for same material are added together
        // Entries without amounts are removed
        $compactedData = [];
        foreach ($componentData as $componentMaterialData) {
            if (isset($componentMaterialData['componentMaterialId'])
                && isset($componentMaterialData['percentageAmount'])
                && $componentMaterialData['percentageAmount'] > 0)
            {
                $componentId = $componentMaterialData['componentMaterialId'];

                if (isset($compactedData[$componentId]))
                {
                    // The material component was listed twice, add the amounts
                    // to a single row
                    $compactedData[$componentId]['percentageAmount'] +=
                        $componentMaterialData['percentageAmount'];
                    // TODO: Not much we can do about the is_additional flag in this case
                }
                else
                {
                    $compactedData[$componentId] = [
                        'componentMaterialId' => $componentId,
                        'percentageAmount' => $componentMaterialData['percentageAmount'],
                        'isAdditional' => false
                    ];
                    if (isset($componentMaterialData['isAdditional'])
                        && $componentMaterialData['isAdditional'] == true) {
                        $compactedData[$componentId]['isAdditional'] = true;
                    }
                }
            }
        }

        $componentMaterials = [];

        // Now add child materials
        foreach ($compactedData as $componentMaterialData)
        {
            $componentMaterial = new MaterialMaterial();
            $componentMaterial->parent_material_id = $parentId;
            $componentMaterial->component_material_id = $componentMaterialData['componentMaterialId'];
            $componentMaterial->percentage_amount = $componentMaterialData['percentageAmount'];
            $componentMaterial->is_additional = $componentMaterialData['isAdditional'];
            $componentMaterial->save();
            $componentMaterials[] = $componentMaterial;
        }

        // We have modified the materials, now need to re-calculate total analysis
        // Also note that the components we retrieve will be correctly ordered for hash
        $materialRepository = new MaterialRepository();
        $material = $materialRepository->getWithDetails($parentId);
        if (!$material)
        {
            throw new Exception('Cannot find material after adding components!');
        }

        // Update analysis and component hashes
        $this->updateAnalysis($material);

        $this->setComponentHashes($material);

        $material->save(); // Required to save the hashes

        return $material;
    }


    public function updateAnalysis($material)
    {

        if (!$material->is_primitive) {

            $compositeMaterial = new CompositeMaterial();

            $components = $material->components;

            foreach ($components as $component) {
                $primitiveMaterial = $component->component_material->getPrimitiveMaterial();

                $compositeMaterial->addMaterial(
                    $primitiveMaterial,
                    $component->percentage_amount,
                    $component->is_additional
                );

            }

            $analysis = null;
            if (!$material->analysis) {
                $analysis = new MaterialAnalysis();
                $analysis->material_id = $material->id;
            }
            else {
                $analysis = $material->analysis;
            }

            $analysis->setAnalysis($compositeMaterial);

            $analysis->save();
        }
    }

    /**
     * Assumes consistent ordering:
     *      first by is_additional
     *      second by percentage_amount
     *      third by material_id (in case two recipe materials have same amount)
     * Assumes Recipe Materials contain Material
     * Doesn't actually update the recipe.
     *
     * @param $components
     */
    public function setComponentHashes($material)
    {
        if ($material->components)
        {
            $hash = ''; // not including additives
            $additive_hash = ''; // all components plus additives
            $hasEntry = false;
            $additiveHasEntry = false;

            foreach ($material->components as $component)
            {
                // For comparison, only use one decimal place
                $amount = number_format($component->percentage_amount,0);
                if ($amount > 0.0001)
                {
                    // TODO: is_theoretical
                    // TODO: trailing pipe
//                    if (!$component->material->is_theoretical && isset($component->material->parent_id))
                    if ($component->component_material->is_primitive && isset($component->component_material->parent_id))
                    {
                        // TODO: JUST assume this is a primitive material with theoretical parent??
                        // This material has a theoretical parent.  Use the parent in the hash.
                        $additive_hash .= $component->component_material->parent_id.":".$amount."|";
                        $additiveHasEntry = true;

                        if (!$component->is_additional) {
                            $hash .= $component->component_material->parent_id.":".$amount."|";
                            $hasEntry = true;
                        }
                    }
                    else
                    {
                        $additive_hash .= $component->component_material_id.":".$amount."|";
                        $additiveHasEntry = true;

                        if (!$component->is_additional) {
                            $hash .= $component->component_material_id.":".$amount."|";
                            $hasEntry = true;
                        }
                    }
                }
            }
            $material->base_composite_hash = $hash;
            $material->additive_composite_hash = $additive_hash;
        }
        else
        {
            // No materials for this recipe, set hashes to null
            $this->base_composite_hash = null;
            $this->additive_composite_hash = null;
        }
    }

}
