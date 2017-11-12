<?php

namespace App\Api\V1\Transformers\UserMaterial;

use App\Api\V1\Transformers\User\UserTransformer;
use App\Api\V1\Transformers\Material\MaterialTransformer;
use App\Models\UserMaterial;
use League\Fractal;

class UserMaterialTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
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
            $ldap_data['stockAmount'] = $userMaterial['stockAmount'];
            $ldap_data['price'] = $userMaterial['price'];
            $ldap_data['vendor'] = $userMaterial['vendor'];
            $ldap_data['vendor_code'] = $userMaterial['vendor_code'];
            $ldap_data['created_at'] = $userMaterial['created_at'];
            $ldap_data['updated_at'] = $userMaterial['updated_at'];
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