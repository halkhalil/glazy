<?php

namespace App\Api\V1\Transformers\UserProfile;

use App\Models\Glazy\Material\UserProfile;

use League\Fractal;

class UserProfileTransformer extends Fractal\TransformerAbstract
{

    const JSON_NAMES = [
        UserProfile::DB_USERNAME        => 'username',
        UserProfile::DB_TITLE           => 'title',
        UserProfile::DB_DESCRIPTION     => 'description',
        UserProfile::DB_URL             => 'url',
        UserProfile::DB_PINTEREST       => 'pinterest',
        UserProfile::DB_FACEBOOK        => 'facebook',
        UserProfile::DB_INSTAGRAM       => 'instagram'
    ];

    public function transform(UserProfile $user_profile)
    {
        $user_profile_data = [];

        $user_profile_data[self::JSON_NAMES[UserProfile::DB_USERNAME]] = $user_profile[UserProfile::DB_USERNAME];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_TITLE]] = $user_profile[UserProfile::DB_TITLE];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_DESCRIPTION]] = $user_profile[UserProfile::DB_DESCRIPTION];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_URL]] = $user_profile[UserProfile::DB_URL];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_PINTEREST]] = $user_profile[UserProfile::DB_PINTEREST];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_FACEBOOK]] = $user_profile[UserProfile::DB_FACEBOOK];
        $user_profile_data[self::JSON_NAMES[UserProfile::DB_INSTAGRAM]] = $user_profile[UserProfile::DB_INSTAGRAM];

        // TODO
        // if ($user_profile->thumbnail_filename) {
        // }

        return $user_profile_data;
    }
}