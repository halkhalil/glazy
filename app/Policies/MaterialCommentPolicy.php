<?php

namespace App\Policies;

use App\User;
use App\Models\MaterialComment;

use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the comment.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialComment  $materialComment
     * @return mixed
     */
    public function view(User $user, MaterialComment $materialComment)
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
     * @param  \App\MaterialComment  $materialComment
     * @return mixed
     */
    public function update(User $user, MaterialComment $materialComment)
    {
        return $user->id === $materialComment->user_id;
    }

    /**
     * Determine whether the user can delete the material.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialComment  $materialComment
     * @return mixed
     */
    public function delete(User $user, MaterialComment $materialComment)
    {
        return $user->id === $materialComment->user_id;
    }



}
