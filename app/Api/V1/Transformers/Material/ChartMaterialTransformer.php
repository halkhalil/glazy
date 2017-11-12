<?php

namespace App\Api\V1\Transformers;

use App\Models\MaterialType;
use App\Models\OrtonCone;
use App\Models\SurfaceType;
use App\Models\TransparencyType;
use Illuminate\Support\Facades\Log;

class ChartMaterialTransformer extends Transformer
{
    public function transform($material)
    {
        $ortonCones = new OrtonCone();
        $materialType = new MaterialType();
        $surfaceType = new SurfaceType();
        $transparencyType = new TransparencyType();

        $shallowMaterialImageTransformer = new ShallowMaterialImageTransformer();

        $shallowUserTransformer = new ShallowUserTransformer();

        $material_data = [];

        $material_data['id'] = $material['id'];
        $material_data['name'] = $material['name'];

        $material_data['is_primitive'] = (boolean) $material['is_primitive'];
        if ($material['material_type_id'])
        {
            $material_data['material_type_id'] = $material['material_type_id'];
            $material_data['base_type_id'] = $materialType->getBaseType($material['material_type_id']);
            $material_data['material_type'] = $materialType->getLineageIdNameArray($material['material_type_id']);
        }

        $material_data['is_analysis'] = (boolean) $material['is_analysis'];


        if ($material['from_orton_cone_id']) {
            $material_data['from_orton_cone_id'] = $material['from_orton_cone_id'];
            $material_data['from_orton_cone_name'] = $ortonCones->getValue($material['from_orton_cone_id']);
        }

        if ($material['to_orton_cone_id']) {
            $material_data['to_orton_cone_id'] = $material['to_orton_cone_id'];
            $material_data['to_orton_cone_name'] = $ortonCones->getValue($material['to_orton_cone_id']);
        }

        if ($material['surface_type_id']) {
            $material_data['surface_type_id'] = $material['surface_type_id'];
            $material_data['surface_type_name'] = $surfaceType->getValue($material['surface_type_name']);
        }

        if ($material['transparency_type_id']) {
            $material_data['transparency_type_id'] = $material['transparency_type_id'];
            $material_data['transparency_type_name'] = $transparencyType->getValue($material['transparency_type_name']);
        }

        // TODO: add thumbnail url & info
        if ($material['thumbnail_id']) {
            $material_data['thumbnail_id'] = $material['thumbnail_id'];

            if ($material->thumbnail) {
                $material_data['thumbnail'] = $shallowMaterialImageTransformer
                    ->transform($material->thumbnail);
            }
        }

        $material_data['is_private'] = (boolean) $material['is_private'];

        $material_data['created_by_user_id'] = $material['created_by_user_id'];

        if ($material->created_by_user) {
            $material_data['created_by_user'] = $shallowUserTransformer->transform($material->created_by_user);
        }

        $material_data['created_at'] = $material['created_at'];
        $material_data['updated_at'] = $material['updated_at'];

        $materialAnalysisTransformer = new MaterialAnalysisTransformer();
        $analysis_data = $materialAnalysisTransformer->transform($material->analysis);
        $material_data['analysis'] = $analysis_data;

        return $material_data;
    }
}