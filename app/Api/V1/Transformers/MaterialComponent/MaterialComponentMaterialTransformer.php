<?php

namespace App\Api\V1\Transformers\MaterialComponent;

use App\Api\V1\Transformers\MaterialAnalysis\MaterialAnalysisTransformer;

use App\Models\Material;

use League\Fractal;

class MaterialComponentMaterialTransformer extends Fractal\TransformerAbstract
{
    const DB_ID = 'id';
    const DB_NAME = 'name';
    const DB_IS_ANALYSIS = 'is_analysis';
    const DB_IS_PRIMITIVE = 'is_primitive';
    const DB_IS_PRIVATE = 'is_private';

    const JSON_NAMES = [
        self::DB_ID             => 'id',
        self::DB_NAME           => 'name',
        self::DB_IS_ANALYSIS    => 'isAnalysis',
        self::DB_IS_PRIMITIVE   => 'isPrimitive',
        self::DB_IS_PRIVATE     => 'isPrivate'
    ];

    protected $defaultIncludes = [
        'analysis'
    ];

    public function transform(Material $material)
    {
        $material_data = [];

        $material_data[self::JSON_NAMES[self::DB_ID]] = $material[self::DB_ID];
        $material_data[self::JSON_NAMES[self::DB_NAME]] = $material[self::DB_NAME];
        $material_data[self::JSON_NAMES[self::DB_IS_ANALYSIS]] = (boolean) $material[self::DB_IS_ANALYSIS];
        $material_data[self::JSON_NAMES[self::DB_IS_PRIMITIVE]] = (boolean) $material[self::DB_IS_PRIMITIVE];
        $material_data[self::JSON_NAMES[self::DB_IS_PRIVATE]] = (boolean) $material[self::DB_IS_PRIVATE];

        return $material_data;
    }

    public function includeAnalysis(Material $material)
    {
        return $this->item($material->analysis, new MaterialAnalysisTransformer());
    }

}