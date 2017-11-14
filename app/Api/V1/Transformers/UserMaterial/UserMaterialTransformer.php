<?php

namespace App\Api\V1\Transformers\UserMaterial;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Api\V1\Transformers\Material\MaterialTransformer;
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
        $ldap_data = array();

        if ($userMaterial)
        {
            $ldap_data['id'] = $userMaterial['id'];
            $ldap_data['userId'] = $userMaterial['user_id'];
            $ldap_data['materialId'] = $userMaterial['material_id'];
            $ldap_data['stockAmount'] = $userMaterial['stock_amount'];
            $ldap_data['price'] = $userMaterial['price'];
            $ldap_data['vendor'] = $userMaterial['vendor'];
            $ldap_data['vendorCode'] = $userMaterial['vendor_code'];
            $ldap_data['createdAt'] = $this->jsonDate($userMaterial['created_at']);
            $ldap_data['updatedAt'] = $this->jsonDate($userMaterial['updated_at']);
        }

        return $ldap_data;
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
            return $this->item($userMaterial->material, new MaterialTransformer());
        }
    }

}