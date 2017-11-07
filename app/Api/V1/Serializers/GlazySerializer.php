<?php

namespace App\Api\V1\Serializers;

use League\Fractal\Serializer\DataArraySerializer;

class GlazySerializer extends DataArraySerializer
{
    public function mergeIncludes($transformedData, $includedData)
    {
        $includedData = array_map(function ($include) {
            return $include['data'];
        }, $includedData);

        return parent::mergeIncludes($transformedData, $includedData);
    }
}