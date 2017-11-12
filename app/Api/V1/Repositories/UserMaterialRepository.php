<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\UserMaterial;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMaterialRepository extends Repository
{
    public function getModel()
    {
        return new UserMaterial();
    }

    public function get($id)
    {
        return UserMaterial::find($id);
    }

    public function getWithDetails($id)
    {
        return UserMaterial::with('material')->with('user')->find($id);
    }

    public function getByUserId()
    {
        $user_id =  Auth::user()->id;
        return UserMaterial::where('user_id', $user_id)->get();
    }

    public function create(array $data)
    {
        $material_id = $data['material_id'];
        $user_id =  Auth::user()->id;

        $material = Material::find($material_id);

        if (!$material) {
            return false;
        }

        $userMaterial = $this->getModel();

        $userMaterial->fill($data);

        $userMaterial->material_id = $material->id;
        $userMaterial->user_id = $user_id;

        $userMaterial->save();

        return $userMaterial;
    }

    public function update(Model $userMaterial, array $data)
    {
        $material = Material::find($userMaterial->material_id);

        if (!$material) {
            return false;
        }

        $userMaterial->fill($data);

        $userMaterial->save();

        return $userMaterial;
    }

    public function destroy(Model $userMaterial)
    {
        $userMaterial->delete();
    }
}
