<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;

use App\Models\UserProfile;

use App\User;
use Illuminate\Database\Eloquent\Model;


class UserProfileRepository extends Repository
{
    public function getModel()
    {
        return new UserProfile();
    }

    public function get($id)
    {
        return UserProfile::find($id);
    }

    public function getByUserIdWithDetails($user_id)
    {
        return UserProfile::where('user_id', $user_id)->with('user')->first();
    }

    /*
    public function getAll()
    {
        return UserProfile::get();
    }
    */

    public function create(User $user, array $data)
    {
        $userProfile = $this->getModel();
        
        $userProfile->fill($data);

        $userProfile->user_id = $user->id;

        $userProfile->save();

        return $userProfile;
    }

    public function update(Model $userProfile, array $data)
    {
        $userProfile->fill($data);

        $userProfile->save();

        return $userProfile;
    }
}
