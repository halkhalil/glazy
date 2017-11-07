<?php

namespace App\Api\V1\Transformers\MaterialComponent;

use App\Models\Glazy\Material\MaterialMaterial;

use League\Fractal;

class MaterialComponentTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'material'
    ];

    public function transform(MaterialMaterial $materialComponent)
    {
        $material_data = [];

        if ($materialComponent)
        {
            $material_data = [
                'percentageAmount' => $materialComponent->percentage_amount,
                'isAdditional' => (boolean) $materialComponent->is_additional,
            ];
        }

        return $material_data;
    }

    public function includeMaterial(MaterialMaterial $materialComponent)
    {
        if ($materialComponent->component_material) {
            return $this->item($materialComponent->component_material, new MaterialComponentMaterialTransformer());
        }
    }

}