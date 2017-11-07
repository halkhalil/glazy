<?php

namespace App\Policies;

use App\User;
use App\Material;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the material.
     *
     * @param  \App\User  $user
     * @param  \App\Material  $material
     * @return mixed
     */
    public function view(User $user, Material $material)
    {
        if($user->id === $material->created_by_user_id) {
            return true;
        }
        if(!$material->is_private) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create materials.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the material.
     *
     * @param  \App\User  $user
     * @param  \App\Material  $material
     * @return mixed
     */
    public function update(User $user, Material $material)
    {
        return $user->id === $material->created_by_user_id;
    }

    /**
     * Determine whether the user can delete the material.
     *
     * @param  \App\User  $user
     * @param  \App\Material  $material
     * @return mixed
     */
    public function delete(User $user, Material $material)
    {
        return $user->id === $material->created_by_user_id;
    }
}
