<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Repository;
use App\Models\UserProfile;
use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Api\V1\Repositories\Filesystem\GlazyImageFile;


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
        // TODO: Use Request subclass and validation
        $userProfile->fill($data);

        $userProfile->save();

        return $userProfile;
    }

    /**
     * Update user profile with new avatar image.
     *
     * @param Model $material
     * @param array $data
     * @return MaterialImage
     */
    public function createAvatar(Model $userProfile, array $data)
    {
        if (!$data['imageFile']) {
            throw new Exception('Upload file not found');
        }

        // Store resized images on filesystem
        $glazyImageFile = new GlazyImageFile(
            GlazyImageFile::PROFILES_STORAGE_NAME,
            $userProfile->id,
            $data['imageFile']
        );

        $glazyImageFile->store();

        if ($userProfile->image_filename) {
            // New image stored, now try to delete existing avatar file
            GlazyImageFile::deleteFiles(
                GlazyImageFile::PROFILES_STORAGE_NAME,
                $userProfile->id,
                $userProfile->image_filename
            );
        }

        $userProfile->image_filename = $glazyImageFile->getFilename();

        // Finally we can save the image
        $userProfile->save();

        return $userProfile;
    }
}
