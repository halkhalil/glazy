<?php

namespace App\Api\V1\Transformers\MaterialComponent;

use League\Fractal;

class ShallowMaterialComponentTransformer extends Fractal\TransformerAbstract
{
    public function transform($materialComponent)
    {
        $material_data = [];

        if ($materialComponent)
        {
            $material_data = [
                'material' => $this->transformMaterial($materialComponent->component_material),
                'percentageAmount' => $materialComponent->percentage_amount,
                'isAdditional' => (boolean) $materialComponent->is_additional
            ];
        }

        return $material_data;
    }

    protected function transformMaterial($material)
    {
        $material_data = [];

        $material_data['id'] = $material['id'];
        $material_data['name'] = $material['name'];
        $material_data['isPrivate'] = (boolean) $material['is_private'];

        return $material_data;
    }

}

