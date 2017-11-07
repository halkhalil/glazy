<?php

namespace App\Api\V1\Transformers\Material;

use App\Api\V1\Transformers\Material\ShallowMaterialTransformer;

use League\Fractal;

class ShallowMaterialFromMaterialImageTransformer extends Fractal\TransformerAbstract
{
    public function transform($materialImage)
    {
        $material = $materialImage->material;
        $transformer = new ShallowMaterialTransformer();

        return $transformer->transform($material);
    }
}