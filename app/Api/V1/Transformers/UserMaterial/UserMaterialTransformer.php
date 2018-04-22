<?php

namespace App\Api\V1\Transformers\UserMaterial;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Api\V1\Transformers\Material\ShallowMaterialTransformer;
use App\Models\UserMaterial;
use League\Fractal;

use App\Api\V1\Transformers\JsonDateTransformer;

class UserMaterialTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    protected $availableIncludes = [
        'user'
    ];

    protected $defaultIncludes = [
        'material'
    ];

    public function transform($userMaterial)
    {
        $json_data = array();

        if ($userMaterial)
        {
            $json_data['id'] = $userMaterial['id'];
            $json_data['userId'] = $userMaterial['user_id'];
            $json_data['materialId'] = $userMaterial['material_id'];
            $json_data['stockAmount'] = $userMaterial['stock_amount'];
            $json_data['price'] = $userMaterial['price'];
            $json_data['vendor'] = $userMaterial['vendor'];
            $json_data['vendorCode'] = $userMaterial['vendor_code'];
            $json_data['createdAt'] = $this->jsonDate($userMaterial['created_at']);
            $json_data['updatedAt'] = $this->jsonDate($userMaterial['updated_at']);
        }

        return $json_data;
    }

    public function includeUser(UserMaterial $userMaterial)
    {
        if ($userMaterial->user) {
            return $this->item($userMaterial->user, new UserTransformer());
        }
    }

    public function includeMaterial(UserMaterial $userMaterial)
    {
        if ($userMaterial->material) {
            return $this->item($userMaterial->material, new ShallowMaterialTransformer());
        }
    }

}