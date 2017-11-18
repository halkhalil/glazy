<?php

namespace App\Policies;

use App\User;
use App\Models\MaterialReview;

use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the review.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialReview  $materialReview
     * @return mixed
     */
    public function view(User $user, MaterialReview $materialReview)
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
     * @param  \App\MaterialReview  $materialReview
     * @return mixed
     */
    public function update(User $user, MaterialReview $materialReview)
    {
        return $user->id === $materialReview->user_id;
    }

    /**
     * Determine whether the user can delete the material.
     *
     * @param  \App\User  $user
     * @param  \App\MaterialReview  $materialReview
     * @return mixed
     */
    public function delete(User $user, MaterialReview $materialReview)
    {
        return $user->id === $materialReview->user_id;
    }



}
