<?php

namespace App\Api\V1\Transformers\Collection;

use App\Api\V1\Transformers\JsonDateTransformer;
use App\Models\Collection;

use League\Fractal;

class CollectionTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    public function transform(Collection $collection)
    {

        $collection_data = [];

        if ($collection)
        {
            $collection_data = [
                'id' => $collection->id,
                'name' => $collection->name
            ];
        }

        return $collection_data;
    }
}