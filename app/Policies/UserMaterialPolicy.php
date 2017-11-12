<?php

namespace App\Policies;

use App\User;
use App\Models\UserMaterial;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserMaterialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the material.
     *
     * @param  \App\User  $user
     * @param  \App\UserMaterial  $userMaterial
     * @return mixed
     */
    public function view(User $user, UserMaterial $userMaterial)
    {
        if($user->id === $userMaterial->created_by_user_id) {
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
     * @param  \App\UserMaterial  $userMaterial
     * @return mixed
     */
    public function update(User $user, UserMaterial $userMaterial)
    {
        return $user->id === $userMaterial->created_by_user_id;
    }

    /**
     * Determine whether the user can delete the material.
     *
     * @param  \App\User  $user
     * @param  \App\UserMaterial  $userMaterial
     * @return mixed
     */
    public function delete(User $user, UserMaterial $userMaterial)
    {
        return $user->id === $userMaterial->created_by_user_id;
    }


}
