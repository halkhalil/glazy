<?php

namespace App\Api\V1\Transformers\Material;

use App\Api\V1\Transformers\Atmosphere\SimpleAtmosphereTransformer;
use App\Api\V1\Transformers\MaterialAnalysis\MaterialAnalysisTransformer;
use App\Api\V1\Transformers\MaterialAnalysis\FullMaterialAnalysisTransformer;
use App\Api\V1\Transformers\MaterialComponent\ShallowMaterialComponentTransformer;
use App\Api\V1\Transformers\MaterialImage\ShallowMaterialImageTransformer;
use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\OrtonCone;
use App\Models\SurfaceType;
use App\Models\TransparencyType;
use Illuminate\Support\Facades\Log;
use App\Api\V1\Transformers\JsonDateTransformer;

use League\Fractal;

class CalculatorMaterialTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    const MATERIAL_TYPE = 'materialType';
    const BASE_TYPE_ID = 'baseTypeId';
    const HEX_COLOR = 'hexColor';
    const SURFACE_TYPE_NAME = 'surfaceTypeName';
    const TRANSPARENCY_TYPE_NAME = 'transparencyTypeName';
    const MATERIAL_COMPONENT_TOTAL_AMOUNT = 'materialComponentTotalAmount';

    const JSON_NAMES = [
        Material::DB_ID                     => 'id',
        Material::DB_PARENT_ID              => 'parentId',
        Material::DB_NAME                   => 'name',
        Material::DB_IS_ANALYSIS            => 'isAnalysis',
        Material::DB_IS_PRIMITIVE           => 'isPrimitive',
        Material::DB_IS_PRIVATE             => 'isPrivate',
        Material::DB_MATERIAL_TYPE_ID       => 'materialTypeId',
        Material::DB_CREATED_BY_USER_ID     => 'createdByUserId',
        Material::DB_CREATED_AT             => 'createdAt',
        Material::DB_UPDATED_AT             => 'updatedAt'
    ];

    protected $defaultIncludes = [
        'analysis',
    ];

    public function transform(Material $material)
    {
        $material_data = [];

        $material_data[self::JSON_NAMES[Material::DB_ID]] = $material[Material::DB_ID];
        $material_data[self::JSON_NAMES[Material::DB_PARENT_ID]] = $material[Material::DB_PARENT_ID];
        $material_data[self::JSON_NAMES[Material::DB_NAME]] = $material[Material::DB_NAME];
        $material_data[self::JSON_NAMES[Material::DB_IS_ANALYSIS]] = (boolean) $material[Material::DB_IS_ANALYSIS];
        $material_data[self::JSON_NAMES[Material::DB_IS_PRIMITIVE]] = (boolean) $material[Material::DB_IS_PRIMITIVE];

        $materialType = new MaterialType();

        if ($material[Material::DB_MATERIAL_TYPE_ID])
        {
            $material_data[self::JSON_NAMES[Material::DB_MATERIAL_TYPE_ID]] = $material[Material::DB_MATERIAL_TYPE_ID];
            $material_data[self::BASE_TYPE_ID] =
                $materialType->getBaseType($material[Material::DB_MATERIAL_TYPE_ID]);
            $material_data[self::MATERIAL_TYPE] =
                $materialType->getSimpleLineageIdNameArray($material[Material::DB_MATERIAL_TYPE_ID]);
        }

        $material_data[self::JSON_NAMES[Material::DB_IS_PRIVATE]] = (boolean) $material[Material::DB_IS_PRIVATE];

        return $material_data;
    }

    public function includeAnalysis(Material $material)
    {
        return $this->item($material->analysis, new MaterialAnalysisTransformer());
    }

}