<?php

namespace App\Api\V1\Transformers\User;

use App\User;

use App\Api\V1\Transformers\UserProfile\UserProfileTransformer;
use App\Api\V1\Transformers\Collection\CollectionTransformer;
use App\Api\V1\Transformers\JsonDateTransformer;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    use JsonDateTransformer;

    const DB_ID = 'id';
    const DB_EMAIL = 'email';
    const DB_NAME = 'name';
    const DB_CREATED_AT = 'created_at';
    const DB_UPDATED_AT = 'updated_at';

    const JSON_NAMES = [
        self::DB_ID         => 'id',
        self::DB_EMAIL      => 'email',
        self::DB_NAME       => 'name',
        self::DB_CREATED_AT => 'createdAt',
        self::DB_UPDATED_AT => 'updatedAt'
    ];

    protected $availableIncludes = [
        'profile',
        'collections'
    ];
    /*
    protected $defaultIncludes = [
        'profile'
    ];
    */

    public function transform(User $user)
    {
        $user_data = [];

        $user_data[self::JSON_NAMES[self::DB_ID]] = $user[self::DB_ID];
        $user_data[self::JSON_NAMES[self::DB_EMAIL]] = $user[self::DB_EMAIL];
        $user_data[self::JSON_NAMES[self::DB_NAME]] = $user[self::DB_NAME];

        //TODO        $user_data['gravatar'] = gravatar()->get($user->email, ['size' => 50]);
        /*
        if ($user->providers) {
            foreach ($user->providers as $provider) {
                if ($provider->avatar) {
                    $user_data['avatar'] = $provider->avatar;
                    break;
                }
            }
        }
        */

        $user_data[self::JSON_NAMES[self::DB_CREATED_AT]] = $this->jsonDate($user[self::DB_CREATED_AT]);
        $user_data[self::JSON_NAMES[self::DB_UPDATED_AT]] = $this->jsonDate($user[self::DB_UPDATED_AT]);

        return $user_data;
    }

    public function includeProfile(User $user)
    {
        if ($user->profile) {
            return $this->item($user->profile, new UserProfileTransformer());
        }
    }

    public function includeCollections(User $user)
    {
        if ($user->collections && count($user->collections) > 0) {
            return $this->collection($user->collections, new CollectionTransformer());
        }
    }
}