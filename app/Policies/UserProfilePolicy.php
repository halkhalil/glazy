<?php

namespace App\Policies;

use App\Models\Access\User\User;
use App\Models\Glazy\Material\UserProfile;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserProfile $userProfile)
    {
        return true;
    }

    public function create(User $user, UserProfile $userProfile)
    {
        return $user->id === $userProfile->user_id;
    }

    public function update(User $user, UserProfile $userProfile)
    {
        return $user->id === $userProfile->user_id;
    }

    public function delete(User $user, UserProfile $userProfile)
    {
        return $user->id === $userProfile->user_id;
    }


}
