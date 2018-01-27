<?php

namespace App\Api\V1\Transformers\MaterialImage;

use App\Models\Atmosphere;
use App\Models\OrtonCone;
use App\Models\Material;
use App\Models\MaterialImage;
use App\Api\V1\Transformers\JsonDateTransformer;
use App\Api\V1\Transformers\User\UserTransformer;

use League\Fractal;

class MaterialImageTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    const DOMINANT_HEX_COLOR = 'dominantHexColor';
    const SECONDARY_HEX_COLOR = 'secondaryHexColor';

    const JSON_NAMES = [
        MaterialImage::DB_ID                => 'id',
        MaterialImage::DB_MATERIAL_ID       => 'materialId',
        MaterialImage::DB_TITLE             => 'title',
        MaterialImage::DB_DESCRIPTION       => 'description',
        MaterialImage::DB_ORTON_CONE_ID     => 'ortonConeId',
        MaterialImage::DB_ATMOSPHERE_ID     => 'atmosphereId',
        MaterialImage::DB_DOMINANT_RGB_R    => 'dominantRgbR',
        MaterialImage::DB_DOMINANT_RGB_G    => 'dominantRgbG',
        MaterialImage::DB_DOMINANT_RGB_B    => 'dominantRgbB',
        MaterialImage::DB_SECONDARY_RGB_R   => 'secondaryRgbR',
        MaterialImage::DB_SECONDARY_RGB_G   => 'secondaryRgbG',
        MaterialImage::DB_SECONDARY_RGB_B   => 'secondaryRgbB',
        MaterialImage::DB_FILENAME          => 'filename',
        MaterialImage::DB_IS_PRIVATE        => 'isPrivate',
        MaterialImage::DB_CREATED_BY_USER_ID => 'createdByUserId',
        MaterialImage::DB_CREATED_AT        => 'createdAt',
        MaterialImage::DB_UPDATED_AT        => 'updatedAt'
    ];

    protected $defaultIncludes = [
        'createdByUser'
    ];

    public function transform(MaterialImage $materialImage)
    {
        $image_data = [];

        if ($materialImage)
        {
            $material_data[self::JSON_NAMES[Material::DB_ID]] = $materialImage[Material::DB_ID];

            $image_data[self::JSON_NAMES[MaterialImage::DB_ID]] = $materialImage[MaterialImage::DB_ID];
            $image_data[self::JSON_NAMES[MaterialImage::DB_MATERIAL_ID]] = $materialImage[MaterialImage::DB_MATERIAL_ID];
            $image_data[self::JSON_NAMES[MaterialImage::DB_TITLE]] = $materialImage[MaterialImage::DB_TITLE];
            $image_data[self::JSON_NAMES[MaterialImage::DB_DESCRIPTION]] = $materialImage[MaterialImage::DB_DESCRIPTION];

            if ($materialImage[MaterialImage::DB_ORTON_CONE_ID]) {
                $image_data[self::JSON_NAMES[MaterialImage::DB_ORTON_CONE_ID]] = $materialImage[MaterialImage::DB_ORTON_CONE_ID];
            }

            if ($materialImage[MaterialImage::DB_ATMOSPHERE_ID]) {
                $image_data[self::JSON_NAMES[MaterialImage::DB_ATMOSPHERE_ID]] = $materialImage[MaterialImage::DB_ATMOSPHERE_ID];
            }

            if ($materialImage[MaterialImage::DB_DOMINANT_RGB_R]
                && $materialImage[MaterialImage::DB_DOMINANT_RGB_G]
                && $materialImage[MaterialImage::DB_DOMINANT_RGB_B]) {
                $image_data[self::JSON_NAMES[MaterialImage::DB_DOMINANT_RGB_R]] = $materialImage[MaterialImage::DB_DOMINANT_RGB_R];
                $image_data[self::JSON_NAMES[MaterialImage::DB_DOMINANT_RGB_G]] = $materialImage[MaterialImage::DB_DOMINANT_RGB_G];
                $image_data[self::JSON_NAMES[MaterialImage::DB_DOMINANT_RGB_B]] = $materialImage[MaterialImage::DB_DOMINANT_RGB_B];
                $image_data[self::DOMINANT_HEX_COLOR] =
                    Material::getHexFromRGB(
                        $materialImage[MaterialImage::DB_DOMINANT_RGB_R],
                        $materialImage[MaterialImage::DB_DOMINANT_RGB_G],
                        $materialImage[MaterialImage::DB_DOMINANT_RGB_B]
                    );
            }

            if ($materialImage[MaterialImage::DB_SECONDARY_RGB_R]
                && $materialImage[MaterialImage::DB_SECONDARY_RGB_G]
                && $materialImage[MaterialImage::DB_SECONDARY_RGB_B]) {
                $image_data[self::JSON_NAMES[MaterialImage::DB_SECONDARY_RGB_R]] = $materialImage[MaterialImage::DB_SECONDARY_RGB_R];
                $image_data[self::JSON_NAMES[MaterialImage::DB_SECONDARY_RGB_G]] = $materialImage[MaterialImage::DB_SECONDARY_RGB_G];
                $image_data[self::JSON_NAMES[MaterialImage::DB_SECONDARY_RGB_B]] = $materialImage[MaterialImage::DB_SECONDARY_RGB_B];
                $image_data[self::SECONDARY_HEX_COLOR] =
                    Material::getHexFromRGB(
                        $materialImage[MaterialImage::DB_SECONDARY_RGB_R],
                        $materialImage[MaterialImage::DB_SECONDARY_RGB_G],
                        $materialImage[MaterialImage::DB_SECONDARY_RGB_B]
                    );
            }

            if ($materialImage['filename']) {
                $image_data[self::JSON_NAMES[MaterialImage::DB_FILENAME]] = $materialImage[MaterialImage::DB_FILENAME];
            }

            $image_data[self::JSON_NAMES[MaterialImage::DB_IS_PRIVATE]] = (boolean) $materialImage[MaterialImage::DB_IS_PRIVATE];
            $image_data[self::JSON_NAMES[MaterialImage::DB_CREATED_BY_USER_ID]] = $materialImage[MaterialImage::DB_CREATED_BY_USER_ID];
            $image_data[self::JSON_NAMES[MaterialImage::DB_CREATED_AT]] = $this->jsonDate($materialImage[MaterialImage::DB_CREATED_AT]);
            $image_data[self::JSON_NAMES[MaterialImage::DB_UPDATED_AT]] = $this->jsonDate($materialImage[MaterialImage::DB_UPDATED_AT]);
        }

        return $image_data;
    }

    public function includeCreatedByUser(MaterialImage $materialImage)
    {
        return $this->item($materialImage->created_by_user, new UserTransformer());
    }


}