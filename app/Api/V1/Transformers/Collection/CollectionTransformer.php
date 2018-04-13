<?php

namespace App\Api\V1\Transformers\Collection;

use App\Api\V1\Transformers\JsonDateTransformer;
use App\Api\V1\Transformers\User\UserTransformer;
use App\Models\Collection;

use League\Fractal;

class CollectionTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    protected $defaultIncludes = [
        'createdByUser'
    ];

    public function transform(Collection $collection)
    {

        $collection_data = [];

        if ($collection)
        {
            $collection_data = [
                'id' => $collection->id,
                'name' => $collection->name,
                'materialCount' => $collection->material_count
            ];
        }

        return $collection_data;
    }

    public function includeCreatedByUser(Collection $collection)
    {
        if ($collection->created_by_user) {
            return $this->item($collection->created_by_user, new UserTransformer());
        }
    }

}