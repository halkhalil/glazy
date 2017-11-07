<?php

namespace App\Api\V1\Transformers\Material;

use App\Models\Glazy\Material\Material;
use App\Models\Glazy\Material\MaterialType;
use App\Models\Glazy\Material\OrtonCone;
use App\Models\Glazy\Material\SurfaceType;
use App\Models\Glazy\Material\TransparencyType;

use App\Api\V1\Transformers\Atmosphere\SimpleAtmosphereTransformer;
use App\Api\V1\Transformers\MaterialImage\MaterialImageTransformer;
use App\Api\V1\Transformers\MaterialReview\MaterialReviewTransformer;
use App\Api\V1\Transformers\MaterialAnalysis\MaterialAnalysisTransformer;
use App\Api\V1\Transformers\MaterialComponent\MaterialComponentTransformer;
use App\Api\V1\Transformers\User\UserTransformer;

use Illuminate\Support\Collection;
use League\Fractal;

class MaterialTransformer extends Fractal\TransformerAbstract
{

    const MATERIAL_TYPE = 'materialType';
    const BASE_TYPE_ID = 'baseTypeId';
    const HEX_COLOR = 'hexColor';
    const FROM_ORTON_CONE_NAME = 'fromOrtonConeName';
    const TO_ORTON_CONE_NAME = 'toOrtonConeName';
    const SURFACE_TYPE_NAME = 'surfaceTypeName';
    const TRANSPARENCY_TYPE_NAME = 'transparencyTypeName';
    const MATERIAL_COMPONENT_TOTAL_AMOUNT = 'materialComponentTotalAmount';

    const JSON_NAMES = [
        Material::DB_ID                     => 'id',
        Material::DB_NAME                   => 'name',
        Material::DB_DESCRIPTION            => 'description',
        Material::DB_IS_ANALYSIS            => 'isAnalysis',
        Material::DB_IS_PRIMITIVE           => 'isPrimitive',
        Material::DB_IS_PRIVATE             => 'isPrivate',
        Material::DB_MATERIAL_TYPE_ID       => 'materialTypeId',
        Material::DB_FROM_ORTON_CONE_ID     => 'fromOrtonConeId',
        Material::DB_TO_ORTON_CONE_ID       => 'toOrtonConeId',
        Material::DB_SURFACE_TYPE_ID        => 'surfaceTypeId',
        Material::DB_TRANSPARENCY_TYPE_ID   => 'transparencyTypeId',
        Material::DB_COLOR_NAME             => 'colorName',
        Material::DB_THUMBNAIL_ID           => 'thumbnailId',
        Material::DB_RATING_TOTAL           => 'ratingTotal',
        Material::DB_RATING_NUMBER          => 'ratingNumber',
        Material::DB_RATING_AVERAGE         => 'ratingAverage',
        Material::DB_CREATED_BY_USER_ID     => 'createdByUserId',
        Material::DB_CREATED_AT             => 'createdAt',
        Material::DB_UPDATED_AT             => 'updatedAt'
    ];

    protected $defaultIncludes = [
        'atmospheres',
        'materialComponents',
        'analysis',
        'images',
        'thumbnail',
        'reviews',
        'createdByUser'
    ];

    public function transform(Material $material)
    {
        $material_data = [];

        $material_data[self::JSON_NAMES[Material::DB_ID]] = $material[Material::DB_ID];
        $material_data[self::JSON_NAMES[Material::DB_NAME]] = $material[Material::DB_NAME];
        $material_data[self::JSON_NAMES[Material::DB_DESCRIPTION]] = $material[Material::DB_DESCRIPTION];
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

        $ortonCones = new OrtonCone();

        if ($material[Material::DB_FROM_ORTON_CONE]) {
            $material_data[self::JSON_NAMES[Material::DB_FROM_ORTON_CONE_ID]] =
                $material[Material::DB_FROM_ORTON_CONE_ID];
            $material_data[self::FROM_ORTON_CONE_NAME] =
                $ortonCones->getValue($material[Material::DB_FROM_ORTON_CONE_ID]);
        }
        if ($material[Material::DB_TO_ORTON_CONE]) {
            $material_data[self::JSON_NAMES[Material::DB_TO_ORTON_CONE_ID]] = $material[Material::DB_TO_ORTON_CONE_ID];
            $material_data[self::TO_ORTON_CONE_NAME] =
                $ortonCones->getValue($material[Material::DB_TO_ORTON_CONE_ID]);
        }

        $surfaceType = new SurfaceType();

        if ($material[Material::DB_SURFACE_TYPE_ID]) {
            $material_data[self::JSON_NAMES[Material::DB_SURFACE_TYPE_ID]] = $material[Material::DB_SURFACE_TYPE_ID];
            $material_data[self::SURFACE_TYPE_NAME] =
                $surfaceType->getValue($material[Material::DB_SURFACE_TYPE_ID]);
        }

        $transparencyType = new TransparencyType();

        if ($material[Material::DB_TRANSPARENCY_TYPE_ID]) {
            $material_data[self::JSON_NAMES[Material::DB_TRANSPARENCY_TYPE_ID]] =
                $material[Material::DB_TRANSPARENCY_TYPE_ID];
            $material_data[self::TRANSPARENCY_TYPE_NAME] =
                $transparencyType->getValue($material[Material::DB_TRANSPARENCY_TYPE_ID]);
        }

        if ($material[Material::DB_COLOR_NAME]) {
            $material_data[self::JSON_NAMES[Material::DB_COLOR_NAME]] = $material[Material::DB_COLOR_NAME];
        }

        if ($material[Material::DB_RGB_R] && $material[Material::DB_RGB_G] && $material[Material::DB_RGB_B]) {
            $r = dechex ( $material[Material::DB_RGB_R] );
            if (strlen($r) == 1) { $r = '0'.$r; }
            $g = dechex ( $material[Material::DB_RGB_G] );
            if (strlen($g) == 1) { $g = '0'.$g; }
            $b = dechex ( $material[Material::DB_RGB_B] );
            if (strlen($b) == 1) { $b = '0'.$b; }

            $material_data[self::HEX_COLOR] = $r.$g.$b;
        }

        $material_data[self::JSON_NAMES[Material::DB_THUMBNAIL_ID]] = $material[Material::DB_THUMBNAIL_ID];
        $material_data[self::JSON_NAMES[Material::DB_RATING_TOTAL]] = $material[Material::DB_RATING_TOTAL];
        $material_data[self::JSON_NAMES[Material::DB_RATING_NUMBER]] = $material[Material::DB_RATING_NUMBER];
        $material_data[self::JSON_NAMES[Material::DB_RATING_AVERAGE]] = $material[Material::DB_RATING_AVERAGE];
        $material_data[self::JSON_NAMES[Material::DB_IS_PRIVATE]] = (boolean) $material[Material::DB_IS_PRIVATE];
        $material_data[self::JSON_NAMES[Material::DB_CREATED_BY_USER_ID]] = $material[Material::DB_CREATED_BY_USER_ID];
        $material_data[self::JSON_NAMES[Material::DB_CREATED_AT]] = $material[Material::DB_CREATED_AT];
        $material_data[self::JSON_NAMES[Material::DB_UPDATED_AT]] = $material[Material::DB_UPDATED_AT];

        $material_data[self::MATERIAL_COMPONENT_TOTAL_AMOUNT] =
            $this->getMaterialComponentTotalAmount($material->components);

        return $material_data;
    }

    public function includeAtmospheres(Material $material)
    {
        if ($material->atmospheres && count($material->atmospheres) > 0) {
            return $this->collection($material->atmospheres, new SimpleAtmosphereTransformer());
        }
    }

    public function includeMaterialComponents(Material $material)
    {
        if ($material->components && count($material->components) > 0) {
            return $this->collection($material->components, new MaterialComponentTransformer());
        }
    }

    public function includeAnalysis(Material $material)
    {
        return $this->item($material->analysis, new MaterialAnalysisTransformer());
    }

    public function includeImages(Material $material)
    {
        if ($material->images && count($material->images) > 0) {
            return $this->collection($material->images, new MaterialImageTransformer());
        }
    }

    public function includeThumbnail(Material $material)
    {
        // TODO: add thumbnail url & info
        if ($material->thumbnail) {
            return $this->item($material->thumbnail, new MaterialImageTransformer());
        }
    }

    public function includeReviews(Material $material)
    {
        if ($material->reviews && count($material->reviews) > 0) {
            return $this->collection($material->reviews, new MaterialReviewTransformer());
        }
    }

    public function includeCreatedByUser(Material $material)
    {
        return $this->item($material->created_by_user, new UserTransformer());
    }

    protected function getMaterialComponentTotalAmount($components) {
        $total = 0.0;
        if ($components) {
            foreach ($components as $component) {
                $total += $component->percentage_amount;
            }
        }
        return $total;
    }

}