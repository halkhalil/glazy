<?php

namespace App\Api\V1\Transformers\UserProfile;

use App\Api\V1\Repositories\Filesystem\GlazyImageFile;
use App\Models\Country;
use App\Models\UserProfile;

use League\Fractal;

class UserProfileTransformer extends Fractal\TransformerAbstract
{

    const DB_USERNAME = 'username';
    const DB_COUNTRY_ID = 'country_id';
    const DB_TITLE = 'title';
    const DB_DESCRIPTION = 'description';
    const DB_URL = 'url';
    const DB_PINTEREST = 'pinterest';
    const DB_FACEBOOK = 'facebook';
    const DB_INSTAGRAM = 'instagram';

    const JSON_NAMES = [
        UserProfile::DB_USERNAME        => 'username',
        UserProfile::DB_FACEBOOK_AVATAR => 'facebookAvatar',
        UserProfile::DB_GOOGLE_AVATAR   => 'googleAvatar',
        UserProfile::DB_COUNTRY_ID      => 'countryId',
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

        if ($user_profile->image_filename) {
            // Use Glazy profile pic if possible
            // TODO: Specify size?
            $bin = GlazyImageFile::getBin($user_profile->id);
            // Example: http://homestead.app/storage/uploads/profiles/1/m_1.56338c5852888.jpg
            $user_profile_data['avatar'] = GlazyImageFile::IMAGE_URL_PATH.'/'
                .GlazyImageFile::PROFILES_STORAGE_NAME.'/'
                .$bin.'/m_'.$user_profile->image_filename;
        } else if ($user_profile->facebook_avatar) {
            $user_profile_data['avatar'] = $user_profile->facebook_avatar;
        } else if ($user_profile->google_avatar) {
            $user_profile_data['avatar'] = $user_profile->google_avatar;
        }

        $user_profile_data[self::JSON_NAMES[UserProfile::DB_COUNTRY_ID]] = $user_profile[UserProfile::DB_COUNTRY_ID];

        $countryLookup = new Country();
        $user_profile_data['countryName'] = $countryLookup->getValue($user_profile[UserProfile::DB_COUNTRY_ID]);


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