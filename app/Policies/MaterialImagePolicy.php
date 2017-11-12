<?php

namespace App\Policies;

use App\Models\Access\User\User;
use App\Models\Material;
use App\Models\MaterialImage;

use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialImagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the image.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialImage  $materialImage
     * @return mixed
     */
    public function view(User $user, MaterialImage $materialImage)
    {
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
     * @param  \App\MaterialImage  $materialImage
     * @return mixed
     */
    public function update(User $user, MaterialImage $materialImage)
    {
        return true;
        //return $user->id === $materialImage->created_by_user_id;
    }

    /**
     * Determine whether the user can delete the material.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialImage  $materialImage
     * @return mixed
     */
    public function delete(User $user, MaterialImage $materialImage)
    {
        return $user->id === $materialImage->created_by_user_id;
    }



}
