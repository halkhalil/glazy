<?php

namespace App\Api\V1\Repositories\Material;

use App\Api\V1\Repositories\Repository;

use App\Models\Glazy\Material\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MaterialRepository extends Repository
{
    public function getModel()
    {
        return new Material();
    }

    public function get($id)
    {
        return Material::where('is_primitive', true)->find($id);
    }

    public function getWithDetails($id)
    {
        return Material::with('analysis')->with('atmospheres')->with('components')->where('is_primitive', true)->find($id);
    }

    public function getByUserId($user_id)
    {
        return Material::where('is_primitive', true)->where('created_by_user_id', $user_id)->get();
    }

    /**
     * Get a list of user materials.
     *
     * If a $user_id is specified and is different from the current
     * user's id, make sure that only public materials are returned.
     *
     * If no $user_id is specified and the user is logged in, return
     * materials for the current user.
     *
     * If no $user_id is specified and the user is NOT logged in,
     * just get the list of public materials from the Glazy admin.
     *
     * @param $user_id
     * @return mixed
     */
    public function userlist($user_id)
    {
        if (!$user_id) {
            // Get default Glazy materials
            return Material::where('is_primitive', true)
                ->where('created_by_user_id', 1)
                ->get();
        }

        if (Auth::guard('api')->check()) {
//        if (Auth::check()) {
//            $current_user_id = Auth::user()->id;
            $current_user_id = Auth::guard('api')->user()->id;

            if ($current_user_id == $user_id) {
                // user is getting own materials
                return Material::where('is_primitive', true)
                    ->where('created_by_user_id', $user_id)
                    ->get();
            }
        }

        // user getting someone else's materials
        return Material::where('is_primitive', true)
            ->where('created_by_user_id', $user_id)
            ->where('is_private', false)
            ->get();
    }

    /**
     * Get a list of materials for editing a recipe.
     *
     * When editing a recipe, we need a list of all the recipe's materials
     * as well as all of the user's materials (or if not logged in, the Glazy
     * admin materials).  This list is used to modify the recipe.  The recipe
     * may contain materials that did not originally belong to the user,
     * for example when a recipe is copied from another user.
     *
     * @param $recipe_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function editRecipeMaterialList($recipe_id)
    {
        $materialQuery = Material::query();

        // Get all the materials that a user owns or that Glazy owns..
        if (Auth::guard('api')->check()) {
            $current_user_id = Auth::guard('api')->user()->id;

            // get user materials
            $materialQuery->where(function ($query) use ($current_user_id) {
                $query->where('is_primitive', true);
                $query->where('created_by_user_id', $current_user_id);
            });
            // Get default Glazy materials
            $materialQuery->orWhere(function ($query) {
                $query->where('is_primitive', true);
                $query->where('created_by_user_id', 1);
                $query->where('is_private', false);
            });
        }
        else {
            // Get default Glazy materials
            $materialQuery->where(function ($query) {
                $query->where('is_primitive', true);
                $query->where('created_by_user_id', 1);
                $query->where('is_private', false);
            });
        }

        if ($recipe_id) {
            // Get the materials used in this recipe..
            // (DAU:  This might include other recipes.)
            $materialQuery->orWhereIn('id', function($query) use ($recipe_id) {
                $query->selectRaw('component_material_id')
                    ->from('material_materials')
                    ->where('parent_material_id', $recipe_id);
            });
        }

        $materialQuery->with('analysis');

        $materialQuery->orderBy('name', 'asc');

        return $materialQuery->get();
    }


    public function all()
    {
        return Material::where('is_primitive', true)->get();
    }

    public function create(array $data)
    {
        $material = $this->getModel();

        $material->fill($data);
        $user_id =  Auth::user()->id;
        $material->created_by_user_id = $user_id;
        $material->updated_by_user_id = $user_id;
        $material->save();

        return $material;
    }


    public function update(Model $material, array $data)
    {
        $material->fill($data);

        $material->save();

        return $material;
    }

    public function delete(Model $model)
    {
        Material::destroy($model);
    }


}
